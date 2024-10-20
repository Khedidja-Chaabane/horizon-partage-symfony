<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategorieFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('cours', SubmitType::class, [
                'label' => 'Cours',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->add('atelier', SubmitType::class, [
                'label' => 'Atelier',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->add('service', SubmitType::class, [
                'label' => 'Service',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->add('aide', SubmitType::class, [
                'label' => 'Aide',
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
