<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CovoiturageSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('depart', TextType::class, [
                'label' => 'Ville de départ',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Paris']
            ])
            ->add('arrivee', TextType::class, [
                'label' => 'Ville d\'arrivée',
                'required' => false,
                'attr' => ['placeholder' => 'Ex: Lyon']
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
            ])
            ->add('prixMax', MoneyType::class, [
                'label' => 'Prix max.',
                'currency' => 'EUR',
                'required' => false,
                'attr' => ['placeholder' => '30']
            ])
            ->add('ecologique', CheckboxType::class, [
                'label' => 'Voyage écologique uniquement',
                'required' => false,
            ])
            ->add('noteMinimale', ChoiceType::class, [
                'label' => 'Note min. du chauffeur',
                'required' => false,
                'placeholder' => 'Toutes',
                'choices' => [
                    '5 étoiles' => 5,
                    '4 étoiles et +' => 4,
                    '3 étoiles et +' => 3,
                    '2 étoiles et +' => 2,
                    '1 étoile et +' => 1,
                ]
            ])
            ->add('rechercher', SubmitType::class, [
                'label' => '<i class="bi bi-search"></i> Rechercher',
                'label_html' => true,
                'attr' => ['class' => 'btn btn-primary w-100']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'method' => 'GET', // Important pour que les filtres restent dans l'URL
            'csrf_protection' => false, // Pas besoin de CSRF pour un formulaire de recherche en GET
        ]);
    }

    // Retourne une chaîne vide pour que les paramètres de l'URL ne soient pas préfixés (ex: ?depart=... au lieu de ?covoiturage_search[depart]=...)
    public function getBlockPrefix(): string
    {
        return '';
    }
}
