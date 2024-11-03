<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe actuel *',
                'mapped' => false, // Champ non mappé à l'entité pour ne pas écraser l'ancien mot de passe
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisissez votre mot de passe actuel',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre mot de passe actuel',
                    ])
                ]
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe *',
                'mapped' => false, // Champ non mappé pour éviter d'affecter directement l'entité
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisissez votre nouveau mot de passe',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nouveau mot de passe',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{12,}$/',
                        'message' => 'Votre mot de passe doit contenir au moins 12 caractères, incluant au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
                    ])
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
