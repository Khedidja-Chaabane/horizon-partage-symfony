<?php

namespace App\Form;

//use App\Entity\Don;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
         // Champ caché pour stocker le montant sélectionné via les boutons
         ->add('montant', HiddenType::class, [
                'required' => false,
            ])
            ->add('montant_personnalise', IntegerType::class, [
                'required' => false, // Champ optionnel pour le montant personnalisé
                'attr' => [
                    'placeholder' => 'Saisissez votre montant ici',
                ],
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
