<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Contact;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'label' => 'Nom *',
                'required' => true,
                'label_attr' => [
                    'class' => ' mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre nom',
                    'class' => 'form-control w-100' // Champ occupe toute la largeur
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre nom',
                    ]),
                ]
            ])
            ->add('prenom', null, [
                'label' => 'Prénom *',
                'required' => true,
                'label_attr' => [
                    'class' => ' mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre prénom',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir votre prénom',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre adresse email',
                    'class' => 'form-control w-100'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une adresse email',
                    ]),
                ]
            ])
            ->add('objet', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Objet*',
                'placeholder' => 'Selectionnez un objet',
                'required' => true,
                'label_attr' => [
                    'class' => 'mt-2 mb-2'
                ],
                'attr' => [
                    'class' => 'form-control w-100' 
                ]
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Contenu',
                'label_attr' => [
                    'class' => 'mt-2 mb-2' // Espacement du label
                ],
                'attr' => [
                    'placeholder' => 'Saisir votre message',
                    'class' => 'form-control w-100', // Champ occupe toute la largeur
                    'rows' => 20 // Hauteur du champ de texte
                ],
                'required' => true
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
