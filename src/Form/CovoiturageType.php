<?php

namespace App\Form;

use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Repository\VoitureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;


class CovoiturageType extends AbstractType
{
    private Security $security;

    // Injection du service Security via le constructeur
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser(); // Récupération de l'utilisateur connecté

        $builder
            ->add('lieuDepart', TextType::class, [
                'label' => 'Lieu de départ',
                'attr' => [
                    'placeholder' => 'Exemple: Paris',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un lieu de départ',
                    ]),
                ],
            ])
            ->add('lieuArrivee', TextType::class, [
                'label' => 'Lieu d\'arrivée',
                'attr' => [
                    'placeholder' => 'Exemple: Lyon',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer un lieu d\'arrivée',
                    ]),
                ],
            ])
            ->add('dateDepart', DateType::class, [
                'label' => 'Date de départ',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'placeholder' => 'Sélectionnez une date',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une date de départ',
                    ]),
                    new Assert\GreaterThanOrEqual('today', message: 'La date de départ doit être aujourd\'hui ou dans le futur.'),
                ],
            ])
            ->add('heureDepart', TimeType::class, [
                'label' => 'Heure de départ',
                'widget' => 'single_text',
                'input' => 'string',
                'html5' => true,
                'attr' => [
                    'placeholder' => 'HH:MM',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une heure de départ',
                    ]),
                ],
            ])
            ->add('dateArrivee', DateType::class, [
                'label' => 'Date d\'arrivée',
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Sélectionnez une date',
                ],
                'constraints' => [

                    new Assert\GreaterThanOrEqual(propertyPath: 'parent.all[dateDepart].data', message: 'La date d\'arrivée doit être égale ou postérieure à la date de départ.'),
                ],
            ])
            ->add('heureArrivee', TimeType::class, [
                'label' => 'Heure d\'arrivée',
                'widget' => 'single_text',
                'input' => 'string',
                'html5' => true,
                'required' => false,
                'attr' => [
                    'placeholder' => 'HH:MM',
                ],
            ])
            ->add('voiture', EntityType::class, [
                'class' => Voiture::class,
                'choice_label' => function (Voiture $voiture): string {
                    // Personnaliser ce qui est affiché dans la liste déroulante
                    // S'assure que getMarque() ne retourne pas null avant d'appeler getLibelle()
                    $marquelibelle = $voiture->getMarque() ? $voiture->getMarque()->getLibelle() : 'Marque inconnue';
                    return $marquelibelle . ' - ' . $voiture->getModele() . ' (' . $voiture->getImmatriculation() . ')';
                },
                'label' => 'Voiture utilisée',
                'placeholder' => 'Sélectionnez votre voiture',
                'query_builder' => function (VoitureRepository $vr) use ($user) {
                    // Filtrer les voitures de l'utilisateur connecté
                    return $vr->createQueryBuilder('v')
                        ->innerJoin('v.marque', 'm') // Jointure pour pouvoir trier par marque
                        ->where('v.proprietaire = :user')
                        ->setParameter('user', $user)
                        ->orderBy('m.libelle', 'ASC') // Trier par marque
                        ->addOrderBy('v.modele', 'ASC'); // Puis par modèle
                },
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez sélectionner une voiture',
                    ]),
                ],
            ])

            ->add('nbPlaceTotal', IntegerType::class, [
                'label' => 'Nombre de places',
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'Exemple: 3',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer le nombre de places',
                    ]),
                    new Assert\GreaterThan(0, message: 'Le nombre de places doit être positif.'),
                ],
            ])
            ->add('prixPersonne', MoneyType::class, [
                'label' => 'Prix par personne',
                'currency' => 'EUR',
                'attr' => [
                    'placeholder' => 'Exemple: 20',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez entrer le prix par personne, indiquez 0 si c\'est gratuit',
                    ]),
                    new Assert\GreaterThanOrEqual(0, message: 'Le prix doit être positif ou nul.'),
                ],
            ])
            ->add('statut', TextType::class, [
                'label' => 'Statut',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Exemple: Proposé, Confirmé, Annulé',
                ],
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Covoiturage::class,
        ]);
    }
}
