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
                    "class" => "text-success"
                ],
                'attr' => [
                    'placeholder' => 'saisir un titre',
                    'class' => 'border border-success'
                ],
                'row_attr' => [
                    "class" => "shadow p-3 col-md-6"
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
                'placeholder' => 'Sélectionnez une catégorie',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description de l\'annonce',
                'label_attr' => [
                    "class" => "text-success"
                ],
                'attr' => [
                    'placeholder' => 'saisir un descriptif',
                    'class' => 'border border-success', "rows" => 8
                ],
                'required' => true,
                'row_attr' => [
                    "class" => "shadow p-3 col-md-12"
                ]

            ])
            ->add('status', CheckboxType::class, [
                'label' => 'Cochez pour activer l\'annonce / Décochez pour désactiver',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'row_attr' => [
                    "class" => "form-check shadow p-3 col-md-6"
                ]
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
