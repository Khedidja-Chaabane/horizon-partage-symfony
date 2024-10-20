<?php

namespace App\Form;

use App\Entity\User;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomUser', null, [
                'label' => 'Nom *',
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
                'label' => 'Prénom *',
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
                'label' => 'Pseudo *',
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
                'label' => 'Email *',
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
                'label' => 'Mot de passe *',
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
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{12,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 12 caractères, incluant au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
            ])
            //(?=.*[\W_]) : Vérifie qu'il y a au moins un caractère spécial ou un underscore (\W_ = non-lettre/chiffre ou underscore).
            //\W est pratique pour les caractères spéciaux, car il couvre tout ce qui n'est ni une lettre, ni un chiffre, ni un underscore, ce qui correspond bien à ce qu'on entend par "caractère spécial" dans un mot de passe.

            ->add('photoProfile', FileType::class, [
                'label' => 'Photo de profil (Facultatif)',
                'mapped' => false, // Ne pas mapper directement sur l'entité
                'required' => false, // Rendre le champ facultatif
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'class' => 'form-control-file w-100' // Champ occupe toute la largeur
                ]
            ])
            ->add('captcha', CaptchaType::class, [
                'label' => 'Vérification * ', //Vérification anti-robot
                'required' => true,
                'mapped' => false,
                'label_attr' => [
                    'class' => 'mt-3 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Entrez le texte de l’image',
                    'class' => 'form-control w-100 mt-3 mb-2'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
