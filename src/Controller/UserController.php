<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\User;
use App\Entity\UserTimeProjet;
use App\Form\ProjetType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\FloatType;
use PDO;
use SplTempFileObject;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\VarDumper\VarDumper;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $repoUser = $entityManager->getRepository('App\Entity\User');

        $repoProjet = $entityManager->getRepository('App\Entity\Projet');
        $repoUserProjet = $entityManager->getRepository('App\Entity\UserTimeProjet');

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users'           => $repoUser->findAll(),
            'projets' => $repoProjet->findAllRacine(),
            'userProjets' => $repoUserProjet->findAll()
        ]);
    }
    /**
     * @Route("update/{projetId}/user/{userId}", name="update")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction($projetId, $userId = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        // User time projet
        $userTimeProjets = $em->getRepository('App\Entity\UserTimeProjet')->findOneBy([
            'user' => $userId,
            'projet' => $projetId
        ]);
        $userTimeProjets->incrementTime();
        
        // Projet
        $projet = $em->getRepository('App\Entity\Projet')->find($projetId);
        $em->flush();


        $result = [
            'total' => $projet->getTotal(),
            'users' => [],
            'parent' => [
                'id' => 0,
                'total' => 0
            ]
        ];
        $userTimeProjets = $em->getRepository('App\Entity\UserTimeProjet')->findBy([
            'projet' => $projetId
        ]);
        foreach ($userTimeProjets as $userTimeProjet) {
            $result['users'][] = [
                'id' => $userTimeProjet->getUser()->getId(),
                'total' => $userTimeProjet->getTime()
            ];
        }
        return new JsonResponse($result);
    }
    /**
     * @Route("/fileuploadhandler", name="fileuploadhandler")
     */
    public function fileUploadHandler(Request $request) {
        $output = array('uploaded' => false);
        // get the file from the request object
        $file = $request->files->get('file');
        // generate a new filename (safer, better approach)
        // To use original filename, $fileName = $this->file->getClientOriginalName();
        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        // set your uploads directory
        $uploadDir = $this->get('kernel')->getRootDir() . '/../web/uploads/';
        if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }
        if ($file->move($uploadDir, $fileName)) {
            $output['uploaded'] = true;
            $output['fileName'] = $fileName;
        }
        return new JsonResponse($output);
    }
    /**
     * @Route("/newProjet", name="newProjet")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addProject(Request $request)
    {
        $projet = new Projet();
        $projet->setNom('');
        $projet->setImage(null);
        $projet->setParent(null);

        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProjetType::class, $projet, array(
            'entity_manager' => $entityManager,
        ));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // but, the original `$task` variable has also been updated
            $projet = $form->getData();

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            if ($projet->getImage() != null) {
                $file = new File($projet->getImage());
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // moves the file to the directory where brochures are stored
                $file->move(
                    'assets/img/projets',
                    $fileName
                );

                // updates the 'brochure' property to store the PDF file name
                // instead of its contents
                $projet->setImage($fileName);
            }

            $entityManager->persist($projet);

            $entityManager = $this->getDoctrine()->getManager();

            $repoUSer = $entityManager->getRepository('App\Entity\User');
            $allUsers = $repoUSer->findAll();

            foreach ($allUsers as $user)
            {
                $userProjet = new UserTimeProjet();
                $userProjet->setUser($user);
                $userProjet->setProjet($projet);
                $userProjet->setTime(0);
                $entityManager->persist($userProjet);
            }

             $entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('projet/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/remove/{projetId}", name="remove")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeProject($projetId){
        $entityManager = $this->getDoctrine()->getManager();
        $projet = $entityManager->getRepository('App\Entity\Projet')->find($projetId);
        $projetUsers = $projet->getUsersTime();
        $projetEnfant = $projet->getEnfants();
        foreach ($projetEnfant as $enfants){
            $enfantUserTime = $enfants->getUsersTime();
            foreach ($enfantUserTime as $userTimeEnfant) {
                $entityManager->remove($userTimeEnfant);
                $entityManager->remove($enfants);
            }
        }

        foreach ($projetUsers as $projetUser)
        {
            $entityManager->remove($projetUser);
        }
        if (!$projet) {
            throw $this->createNotFoundException('No guest found for id '.$projetId);
        }
        $entityManager->remove($projet);
        $entityManager->flush();
        return $this->redirectToRoute('user');
    }

    public function calculTotalProjet()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $repoProjet = $entityManager->getRepository('App\Entity\Projet');

        $allProject = $repoProjet->findAll();

        foreach ($allProject as $projet) {
            if ($projet->getEnfants() != null) {
                foreach ($projet->getEnfants() as $projetEnfant) {
                    VarDumper::dump($projetEnfant);

                }
            }
        }
    }
    public function showParentAndChild(){
        
    }
    /**
     * @Route("/export", name="export")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ExcelExportAction()
        {
            $response = new StreamedResponse();
            $response->setCallback(function() {
                $handle = fopen('php://output', 'w+');
                $entityManager = $this->getDoctrine()->getManager();
                $projets = $entityManager->getRepository('App\Entity\Projet')->findAll();
                $users = $entityManager->getRepository('App\Entity\User')->findAll();


                $toCsv = array('Nom du Projet','Parent','Total');
                foreach ($users as $user){
                    $toCsv[] = $user->getNom();
                }
                fputcsv($handle, $toCsv,';');

                foreach ($projets as $projet){
                $section = [];
                $projetNom = $projet->getNom();

                /*while ($parent != null) {
                    $section[] = $parent->getNom();
                    $parent = $parent->getParent();
                }

                $section = array_reverse($section);
                $sectionString = implode("/", $section);*/

                // Query data from database
                // Add the data queried from database


                    $toCsv = array($projetNom,$projet->getParent(), $projet->getTotal());

                    foreach ($projet->getUserstime() as $userProjet){
                        $toCsv[] = $userProjet->getTime();
                    }

                    fputcsv(
                        $handle, // The file pointer
                        $toCsv, // The fields
                        ';' // The delimiter
                    );
                }

                fclose($handle);
            });

            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
            $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

            return $response;
        }
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
}
