<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'attr' => [
                    'placeholder' => 'Indiquez votre adresse email'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 25
                    ])
                ],
                'first_options'  => [
                'label' => 'Votre mot de passe',
                'hash_property_path' => 'password',
                'attr' => [
                    'placeholder' => 'Choisissez votre mot de passe']
                ],

                'second_options' => [
                'label' => 'Confirmez votre mot de passe',
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'max' => 25
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Confirmez votre mot de passe'
                ]
                ],
                'mapped' => false,
            ])
            ->add('nom', TextType::class, [
                'label' => 'Votre nom',
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 25
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Indiquez votre nom'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Votre prénom',
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 25
                    ])
                ],
                'attr' => [
                    'placeholder' => 'Indiquez votre prénom'
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Votre numéro de téléphone',
                'attr' => [
                    'placeholder' => 'Indiquez votre numéro de téléphone'
                ]
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Votre adresse',
                'attr' => [
                    'placeholder' => 'Indiquez votre adresse'
                ]
            ])
            ->add('date_naissance', TextType::class, [
                'label' => 'Votre date de naissance',
                'attr' => [
                    'placeholder' => 'Indiquez votre date de naissance'
                ]
            ])
            ->add('photo', TextType::class, [
                'label' => 'Votre photo',
                'attr' => [
                    'placeholder' => 'Mettez votre photo'
                ]
            ])
            ->add('pseudo', TextType::class, [
                'label' => 'Votre pseudo',
                'attr' => [
                    'placeholder' => 'Indiquez votre pseudo'
                ]
            ])
            ->add('date_inscription', TextType::class, [
                'label' => 'Date d\'inscription',
                'attr' => [
                    'placeholder' => 'La date d\'inscription'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class' => 'btn btn-primary' 
                ]
            ])
   
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraint' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'field' => 'email'
                ])
            ],
            'data_class' => User::class,
        ]);
    }
}
