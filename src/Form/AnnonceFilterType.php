<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('offreEmploi', SubmitType::class, [
                'label' => 'Offre d\'emploi',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->add('partenariat', SubmitType::class, [
                'label' => 'Partenariat',
                'attr' => ['class' => 'btn btn-success'],
            ])
            ->add('benevolat', SubmitType::class, [
                'label' => 'Bénévolat',
                'attr' => ['class' => 'btn btn-success'],
            ]);        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
