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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RegistrationFormType extends AbstractType
{
    private ParameterBagInterface $params;

    // Injection du conteneur de paramètres pour accéder aux paramètres de configuration
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Champ Nom
        $builder
            ->add('nomUser', null, [
                'label' => 'Nom *',
                'required' => true,
                'label_attr' => ['class' => 'mt-2 mb-2'],
                'attr' => [
                    'placeholder' => 'Saisir votre nom',
                    'class' => 'form-control w-100',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre nom']),
                ]
            ])

            // Champ Prénom
            ->add('prenomUser', null, [
                'label' => 'Prénom *',
                'required' => true,
                'label_attr' => ['class' => 'mt-2 mb-2'],
                'attr' => [
                    'placeholder' => 'Saisir votre prénom',
                    'class' => 'form-control w-100',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir votre prénom']),
                ]
            ])

            // Champ Pseudo
            ->add('userName', null, [
                'label' => 'Pseudo *',
                'required' => true,
                'label_attr' => ['class' => 'mt-2 mb-2'],
                'attr' => [
                    'placeholder' => 'Saisir un Pseudo',
                    'class' => 'form-control w-100',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un Pseudo']),
                    new Length([
                        'min' => 4,
                        'minMessage' => 'Le Pseudo doit comporter au moins {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Le Pseudo ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])

            // Champ Email
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'required' => true,
                'label_attr' => ['class' => 'mt-2 mb-2'],
                'attr' => [
                    'placeholder' => 'Saisir votre adresse email',
                    'class' => 'form-control w-100',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir une adresse email']),
                ]
            ])

            // Champ Mot de passe
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe *',
                'mapped' => false, // Le mot de passe est géré séparément
                'required' => true,
                'label_attr' => ['class' => 'mt-2 mb-2'],
                'attr' => [
                    'placeholder' => 'Saisir un mot de passe',
                    'class' => 'form-control w-100',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez saisir un mot de passe']),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{12,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 12 caractères, incluant au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ]),
                ],
            ])

            // Champ Photo de profil
            ->add('photoProfile', FileType::class, [
                'label' => 'Photo de profil (Facultatif)',
                'mapped' => false, // Champ non lié directement à l'entité
                'required' => false, // Le champ est facultatif
                'label_attr' => ['class' => 'mt-2 mb-2'],
                'attr' => [
                    'class' => 'form-control-file w-100', // Occupe toute la largeur
                ]
            ]);

        // Condition pour ajouter le CAPTCHA si captcha_disabled est false
       // if (!$this->params->get('captcha_disabled')) {
           // $builder->add('captcha', CaptchaType::class, [
               //// 'label' => 'Vérification *', // CAPTCHA pour vérifier que l'utilisateur est humain
               // 'required' => true,
               // 'mapped' => false,
               // 'label_attr' => ['class' => 'mt-3 mb-2'],
               /// 'attr' => [
                  //  'placeholder' => 'Entrez le texte de l’image',
                  //  'class' => 'form-control w-100 mt-3 mb-2',
              //  ],
           // ]);
        //}
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, // Associe le formulaire à l'entité User
        ]);
    }
}
