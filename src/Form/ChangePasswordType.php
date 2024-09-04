<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', PasswordType::class, [
                'label' => 'Mot de passe actuel *',
                'mapped' => false,
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisissez votre mot de passe',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Votre mot de passe ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe *',
                'mapped' => false,
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
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        'max' => 20,
                        'maxMessage' => 'Votre mot de passe ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ]
            ])
           
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
