<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', null, [
                'label' => 'Titre du post',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-3 mb-3' // Espacement du label
                ],
                'attr' => [
                    'placeholder' => 'Saisir un titre',
                    'class' => 'form-control w-100' // Champ occupe toute la largeur
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un titre',
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Veuillez saisir au minimum 5 caractères',
                        'max' => 255,
                        'maxMessage' => 'Veuillez saisir au maximum 255 caractères',
                    ])
                ]
            ])
            ->add('texte', TextareaType::class, [
                'label' => 'Contenu',
                'label_attr' => [
                    'class' => 'mt-3 mb-3' // Espacement du label
                ],
                'attr' => [
                    'placeholder' => 'Saisir un contenu',
                    'class' => 'form-control w-100', // Champ occupe toute la largeur
                    'rows' => 20 // Hauteur du champ de texte
                ],
                'required' => true
            ])
            ->add('image', FileType::class, [
                'label' => 'Ajouter une image',
                'required' => false,
                'mapped' => false,
                'label_attr' => [
                    'class' => 'mt-3 mb-3' // Espacement du label
                ],
                'attr' => [
                    'class' => 'form-control-file w-50' 
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
