<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
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
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false,
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir un mot de passe',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 50,
                        'maxMessage' => 'Votre mot de passe ne peut pas dépasser {{ limit }} caractères',
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
            ]);    
        
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
