<?php

namespace App\Form;

use Assert\Regex;
use App\Entity\Marque;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'libelle',
                'label' => 'Marque',
                'placeholder' => 'Sélectionnez une marque',
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une marque.']),
                ],
            ])
            ->add('modele', TextType::class, [
                'label' => 'Modèle',
                'attr' => [
                    'placeholder' => 'Ex: Clio',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le modèle.']),
                    new Length(['min' => 1, 'max' => 50, 'minMessage' => 'Le modèle doit contenir au moins {{ limit }} caractère.', 'maxMessage' => 'Le modèle ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('immatriculation', TextType::class, [
                'label' => 'Immatriculation',
                'attr' => [
                    'placeholder' => 'Ex: AB-123-CD',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer l\'immatriculation.']),
                    new Length(['max' => 50, 'maxMessage' => 'L\'immatriculation ne peut pas dépasser {{ limit }} caractères.']),
                ]
            ])

            ->add('energie', TextType::class, [
                'label' => 'Type d\'énergie',
                'attr' => [
                    'placeholder' => 'Ex: Essence, Diesel, Électrique',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez préciser le type d\'énergie.']),
                    new Length(['max' => 50, 'maxMessage' => 'Le type d\'énergie ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('couleur', TextType::class, [
                'label' => 'Couleur',
                'attr' => [
                    'placeholder' => 'Ex: Rouge, Bleu, Vert',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer la couleur.']),
                    new Length(['max' => 50, 'maxMessage' => 'La couleur ne peut pas dépasser {{ limit }} caractères.']),
                ],
            ])
            ->add('date_premiere_immatriculation', TextType::class, [ // L'entité attend une chaîne
                'label' => 'Date de première immatriculation (JJ/MM/AAAA ou AAAA-MM-JJ)',
                'required' => false, // Selon si ce champ est obligatoire ou non
                'attr' => [
                    'placeholder' => 'Ex: 25/12/2010'
                ]
                // Si vous voulez un champ DateType et gérer la conversion string <-> DateTime :
                // 'widget' => 'single_text',
                // 'html5' => true,
                // 'constraints' => [
                //     new Assert\Date(['message' => 'La date de première immatriculation est invalide.'])
                // ]
            ]);
        // Le champ 'proprietaire' sera défini dans le contrôleur, pas dans le formulaire.
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
