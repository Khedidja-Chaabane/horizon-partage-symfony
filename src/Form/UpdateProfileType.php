<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UpdateProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomUser', null, [
                'label' => 'Nom',
                'required' => true,
                'label_attr' => [
                    'class' => ' mt-2 mb-2' 
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre nom',
                    'class' => 'form-control w-100' // Champ occupe toute la largeur
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom',
                    ]),
                ]
            ])
            ->add('prenomUser', null, [
                'label' => 'Prénom',
                'required' => true,
                'label_attr' => [
                    'class' => ' mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre prénom',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom',
                    ]),
                ]
            ])
            ->add('userName', null, [
                'label' => 'Pseudo',
                'required' => true,
                'label_attr' => [
                    'class' => ' mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir un Pseudo',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un Pseudo',
                    ]),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Le Pseudo doit comporter au moins {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Le Pseudo ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre adresse email',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse email',
                    ]),
                ]
            ])
            ->add('photoProfile', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false, // Ne pas mapper directement sur l'entité
                'required' => false, // Rendre le champ facultatif
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'class' => 'form-control-file w-100' // Champ occupe toute la largeur
                ]
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adresse',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'ex: 1 Avenue Maréchal Joffre',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre adresse',
                    ]),
                    new Regex([
                        'pattern' => '/\d+.*[a-zA-Z]+/',
                        'message' => 'L\'adresse doit contenir un numéro et un nom de rue',
                    ]),
                ]
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'ex: 75000',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre code postal',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{5}$/',
                        'message' => 'Le code postal doit être composé de 5 chiffres',
                    ]),
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'ex: Paris',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre ville',
                    ]),
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre numéro de téléphone',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre numéro de téléphone',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{10}$/',
                        'message' => 'Le numéro de téléphone doit comporter exactement 10 chiffres',
                    ]),
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                'label' => 'Sexe',
                'required' => false,
                'choices' => [
                    'Masculin' => 'Masculin',
                    'Féminin' => 'Féminin',
                    'Autre' => 'Autre'
                ],
                'placeholder' => 'Sélectionner votre sexe',
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'class' => 'form-control w-100'
                ]
            ])
            ->add('nationality', TextType::class, [
                'label' => 'Nationalité',
                'required' => false,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre nationalité',
                    'class' => 'form-control w-100'
                ]
            ]);    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
