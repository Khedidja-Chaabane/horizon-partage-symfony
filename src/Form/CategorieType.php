<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom de la catégorie',
                'required' => true,
                'label_attr' => [
                    "class" => "mt-3 mb-3"
                ],
                'required' => true,
                'attr' => [
                    'placeholder' => 'saisir un titre',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuillez remplir ce champs'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
