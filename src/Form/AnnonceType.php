<?php

namespace App\Form;

use App\Entity\Annonce;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', null, [
                'label' => 'titre de l\'annonce',
                'required' => true,
                'label_attr' => [
                    "class" => "mt-3 mb-3"
                ],
                'attr' => [
                    'placeholder' => 'saisir un titre',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuillez saisir un titre'
                    ])
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'label_attr' => [
                    "class" => ' mt-3 mb-3 w-100', // Espacement du label
                ],
                'placeholder' => 'Sélectionnez une catégorie',
                
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'annonce',
                'label_attr' => [
                    "class" => "mt-3 mb-3"
                ],
                'attr' => [
                    'placeholder' => 'saisir un descriptif',
                    'class' => 'form-control w-100',
                    "rows" => 20
                ],
                'required' => true,
            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Cochez pour activer l\'annonce / Décochez pour désactiver ',
                'required' => false,
                'label_attr' => [
                    'class' => 'mt-3 mb-3 w-100' // Espacement du label
                ],
                'attr' => [
                    'class' => 'form-check-input '
                ],
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
