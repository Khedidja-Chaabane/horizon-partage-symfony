<?php

namespace App\Form;

use App\Entity\Don;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('montant', ChoiceType::class, [
                'choices' => [
                    '10€' => 10,
                    '20€' => 20,
                    '50€' => 50,
                    '100€' => 100,
                    'Montant personnalisé' => null, // Pour sélectionner un montant personnalisé
                ],
                'placeholder' => 'Sélectionnez un montant ou entrez un montant personnalisé',
            ])
            ->add('montant_personnalise', IntegerType::class, [
                'required' => false, // Ce champ est optionnel
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas de 'data_class' car ce formulaire ne lie pas directement à une entité
            // 'data_class' => Don::class,
        ]);
    }
}
