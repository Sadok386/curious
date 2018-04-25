<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Entity\User;
use App\Entity\UserTimeProjet;
use Doctrine\DBAL\Types\FloatType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
            'projets' => $repoProjet->findAll(),
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
        return new JsonResponse($projet->getTotal());
    }
    /**
     * @Route("/newProjet", name="newProjet")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addProject(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $projet = new Projet();
        $projet->setNom('Write a blog post');
        $projet->setTotal(0);

        $form = $this->createFormBuilder($projet)
            ->add('nom', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create project'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // but, the original `$task` variable has also been updated
            $projet = $form->getData();

            $entityManager->persist($projet);

            $entityManager = $this->getDoctrine()->getManager();

            $repoUSer = $entityManager->getRepository('App\Entity\User');
            $allUsers = $repoUSer->findAll();

            foreach ($allUsers as $user)
            {
                $userProjet = new UserTimeProjet();
                $userProjet->setUser($user);
                $userProjet->setProjet($projet);
                $entityManager->persist($userProjet);
            }

             $entityManager->flush();

            return $this->redirectToRoute('user');
        }

        return $this->render('projet/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
