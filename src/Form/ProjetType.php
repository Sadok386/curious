<?php

namespace App\Form;

use App\Entity\Projet;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\VarDumper\VarDumper;

class ProjetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Doctrine\ORM\EntityManager $entityManager */
        $entityManager = $options['entity_manager'];
        $repoProjet = $entityManager->getRepository('App\Entity\Projet');

//        $choices = array();
//        foreach ($repoProjet->findAll() as $projet) {
//            $choices[$projet->] = $code;
//        }

        $builder
            ->add('nom', TextType::class)
            ->add('image', FileType::class,
                array(
                    'required' => false,
                    'label' => 'Brochure (PDF file)')
            )
            ->add('parent', EntityType::class, [
                'class' => 'App\Entity\Projet',
                'choices' => $repoProjet->findAll()])
            ->add('save', SubmitType::class, array('label' => 'Create project'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Projet::class,
        ));
        $resolver->setRequired('entity_manager');
    }
}