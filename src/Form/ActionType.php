<?php

namespace App\Form;

use App\Entity\Action;
use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => 'Titre de l\'action',
                'label_attr' => [
                    "class" => 'mt-3 mb-3' // Espacement du label
                ],
                'attr' => [
                    'placeholder' => 'saisir un titre',
                    'class' => 'form-control w-100' // Champ occupe toute la largeur
                ],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Veuillez saisir au minimum 5 caractères',
                        'max' => 255,
                        'maxMessage' => 'Veuillez saisir au maximum 255 caractères',
                    ])
                    
                ]
               
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionnez une catégorie',
                'required' => true,
                'label_attr' => [
                    "class" => ' mt-3 mb-3', // Espacement du label
                    
                ],
                'attr' => [
                    'class' => 'form-control w-100' 
                ]
              
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de l\'action',
                'label_attr' => [
                    'class' => 'mt-3 mb-3'
                ],
                'attr' => [
                    'placeholder' => 'Saisir un descriptif',
                    'class' => 'form-control w-100',
                    "rows" => 20
                ],
                'required' => true,
            ])
            ->add('tarif', MoneyType::class, [
                'currency' => 'EUR',
                'required' => false,  // Permet au champ d'être facultatif
                'label' => 'Tarif en ',
                'label_attr' => ['class' => 'mt-3 mb-3'],
                'attr' => [
                    'placeholder' => 'Saisir un prix',
                    'class' => 'form-control w-25'
                ],
                'help' => 'Le prix doit être supérieur ou égal à 0, ou peut être laissé vide pour indiquer la gratuité',
                'help_attr' => ['class' => 'text-danger mt-2 mb-3'],
                'constraints' => [
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => 'Le prix doit être supérieur ou égal à 0.',
                    ]),
                ],
            ])

            // Ajout d'un champ pour téléverser une nouvelle image
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'Nouvelle image ',
                'label_attr' => [
                    'class' => 'mt-3 mb-3 w-100' // Espacement du label
                ],
                'attr' => [
                    'class' => 'w-50' 
                ],
                 
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date',
                'label_attr' => ['class' => 'mt-3 mb-3 w-100']
            ])
            ->add('horaire', TimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Horaire',
                'label_attr' => ['class' => 'mt-3 mb-3 w-100']
            ])
            ->add('lieu', TextType::class, [
                'required' => false,
                'label' => 'Lieu',
                'label_attr' => ['class' => 'mt-3 mb-3 w-100'],
                'attr' => [
                    'placeholder' => 'Saisir le lieu',
                    'class' => 'form-control w-100'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Action::class,
        ]);
    }
}
