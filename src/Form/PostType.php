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
                'label' => 'titre du produit',
                'required' => true,
                'label_attr' => [
                    "class" => "text-success"
                ],
                'attr' => [
                    'placeholder' => 'saisir un titre',
                    'class' => 'border border-warning'
                ],
                'help' => 'le titre doit etre ......',
                'help_attr' => [
                    'class' => 'text-info text-center'
                ],
                'row_attr' => [
                    "class" => "shadow p-3 col-md-6"
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'veuillez saisir un titre'
                    ]),
                    new Length([
                        'min' => 5,
                        'minMessage' => 'veuillez saisir au minimum 5 caracters',
                        'max' => 50,
                        'maxMessage' => 'veuillez saisir au max 50 caracters',

                    ])
                ]
            ])
            ->add('texte' , TextareaType::class, [
                'label' => 'contenu',
                'label_attr' => [
                    "class" => "text-success"
                ],
                'attr' => [
                    'placeholder' => 'saisir un contenu',
                    'class' => 'border border-warning', "rows" => 8
                ],
                'required' => true,
                'row_attr' => [
                    "class" => "shadow p-3 col-md-12"
                ]
                ])
                ->add('image', FileType::class, [
                'required' => false,
                'mapped' => false
            ]);
            
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
