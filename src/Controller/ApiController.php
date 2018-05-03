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

class ApiController extends Controller
{
    /**
     * @Route("/api/projets", name="projets")
     * @return JsonResponse
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();

        $repoProjet = $entityManager->getRepository('App\Entity\Projet');

        $repoUserProjet = $entityManager->getRepository('App\Entity\UserTimeProjet');
        $repoUser = $entityManager->getRepository('App\Entity\User');

        $repoProjets = $repoProjet->findAll();
        $repoUserProjets = $repoUserProjet->findAll();
        $repoUsers = $repoUser->findAll();

        $allRepo = [
            'projets' => []
        ];

        $counter = 0;
        foreach ($repoProjets as $projet){
            $allRepo['projets'][] = [
                'id' => $projet->getId(),
                'nom' => $projet->getNom(),
                'total' => $projet->getTotal(),
                'users' => []
            ];


            foreach ($repoUsers as $users){
                $allRepo['projets'][$counter]['users'][] = [
                    'id' => $users->getId(),
                    'nom' => $users->getNom(),
                    'time' => $repoUserProjet->findOneBy([
                        'user' => $users->getId(),
                        'projet' => $projet->getId()
                    ])->getTime()
                ];
            }
            $counter++;
        }


        return new JsonResponse($allRepo);
    }

}
