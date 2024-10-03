<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('roles', ChoiceType::class, [
                'choices'  => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                ],
                
                'multiple' => true,
                'expanded' => true, // Checkboxes pour les roles
                
               'label' => 'Roles',
                'label_attr' => [
                    'class' => 'mt-3 mb-3 ' // Espacement du label
                ],
                'attr' => [
                    'class' => 'mt-2 mb-5 '
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
