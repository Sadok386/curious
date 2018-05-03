<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\User;
use App\Entity\UserTimeProjet;
use App\Form\ProjetType;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        $allChild = $repoProjet->findTime(2);
        VarDumper::dump($allChild);
//        $toto=$this->calculTotalProjet();

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
        $projet->incrementTotal();
        $em->flush();

//        $parent = ...

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
     * @Route("/newProjet", name="newProjet")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addProject(Request $request)
    {
        $projet = new Projet();
        $projet->setNom('Write a blog post');
        $projet->setImage(null);
        $projet->setParent(null);
        $projet->setTotal(0);

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
    public function ExcelExportAction(Request $request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
        $writer = new Xlsx($spreadsheet);
        $writer->save('hello world.xlsx');
    }
}
