<?php

namespace App\Form;

use App\Entity\Action;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', null, [
                'label' => 'titre de l\'action',
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
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'veuillez saisir au minimum 5 caracters',
                        'max' => 50,
                        'maxMessage' => 'veuillez saisir au max 100 caracters',

                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description de l\'action',
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
            ->add('tarif', MoneyType::class, [
                'currency' => 'EUR',
                'required' => false, 
                'label' => 'Tarif',
                'label_attr' => ['class' => 'text-success'],
                'attr' => [
                    'placeholder' => 'Saisir un prix',
                    'class' => 'border border-success'
                ],
                'help' => 'Le prix doit être supérieur à 0',
                'help_attr' => ['class' => 'text-danger text-center'],
                'constraints' => [
                    new Positive(['message' => 'Le prix doit être supérieur à 0']),
                ]
            ])
            
            // Ajout d'un champ pour téléverser une nouvelle image
            ->add('image', FileType::class, [
                'required' => false, // Non requis, car l'utilisateur peut garder l'image actuelle
                'mapped' => false,
                'label' => 'Nouvelle image ',
                'attr' => ['readonly' => true],
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => false, 
                'label' => 'Date',
                'label_attr' => ['class' => 'text-success']
            ])
            ->add('horaire', TimeType::class, [
                'widget' => 'single_text',
                'required' => false, 
                'label' => 'Horaire',
                'label_attr' => ['class' => 'text-success']
            ])
            ->add('lieu', TextType::class, [
                'required' => false, 
                'label' => 'Lieu',
                'label_attr' => ['class' => 'text-success'],
                'attr' => [
                    'placeholder' => 'Saisir le lieu',
                    'class' => 'border border-success'
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
