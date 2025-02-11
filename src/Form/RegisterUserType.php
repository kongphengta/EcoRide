<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Voiture;
use App\Entity\Configuration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'attr' => [
                    'placeholder' => 'Veuillez entrer votre nom',
                ],
                    'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 25,
                        ])
                    ]
                
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Votre prénom',
                'attr' => [
                    'placeholder' => 'Veuillez entrer votre prénom',
                ],
                    'constraints' => [
                        new Length([
                        'min' => 4,
                        'max' => 25,
                        ]),
                    ]
               
            ])
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => [
                    'placeholder' => 'Entrez votre email',
                ],
            ]) 

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 25,
                    ])
                ],
                'first_options'  => [
                    'label' => 'Votre mot de Passe',
                    'hash_property_path' => 'password',
                    'attr' => [
                    'placeholder' => 'Choissisez votre mot de passe',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmez votre mot de passe',
                    'attr' => [
                    'placeholder' => 'Confirmez votre mot de passe',
                    ],
                ],

                'mapped' => false,
            ])
            ->add('numero_telephone', TextType::class, [ 
                'label' => 'Votre numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Entrez votre numéro de téléphone',
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Votre adresse',
                'attr' => [
                    'placeholder' => 'Entrez votre adresse',
                ],
            ])
            ->add('date_naissance', null, [
                'label' => 'Votre date de naissance',
                'attr' => [
                    'placeholder' => 'Entrez votre date de naissance',
                ],
            ])
            ->add('date_inscription', null, [
                'label' => 'Votre date d\'inscription',
                'attr' => [
                    'placeholder' => 'Entrez votre date d\'inscription',
                ],
            ])
            ->add('photo', TextType::class, [
                'label' => 'Votre photo',
                'attr' => [
                    'placeholder' => 'Entrez votre photo',
                ],
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Votre pseudo',
                'attr' => [
                    'placeholder' => 'Entrez votre pseudo',
                ],
            ])
            ->add('configuration', EntityType::class, [
                'class' => Configuration::class,
                'choice_label' => 'id',
            ])
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => 'id',
            ])
            ->add('couleur', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => 'id',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => ['email'],
                ]),
            ],
            'data_class' => User::class,
        ]);
    }
}
