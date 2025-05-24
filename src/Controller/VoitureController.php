<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted; // Pour la sécurité

// #[Route('/voiture')]
class VoitureController extends AbstractController
{
    #[Route('/voiture', name: 'app_voiture_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // seul un utilisateur connecté peut voir ses voitures
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            // Gérer le cas où l'utilisateur n'est pas connecté, bien que IsGranted devrait le faire
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les voitures de l'utilisateur connecté
        $voitures = $entityManager
            ->getRepository(Voiture::class)
            ->findBy(['proprietaire' => $user]);

        return $this->render('voiture/index.html.twig', [
            'controller_name' => 'VoitureController',
            'voitures' => $voitures, // Passer les voitures au template
        ]);
    }

    #[Route('/ajouter', name: 'app_voiture_ajouter', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')] // Seul un utilisateur connecté peut ajouter une voiture
    public function ajouterVoiture(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voiture = new Voiture();
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if (!$user) {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            // Normalement, IsGranted s'en charge, mais c'est une double sécurité.
            $this->addFlash('warning', 'Vous devez être connecté pour ajouter une voiture.');
            return $this->redirectToRoute('app_login');
        }
        $voiture->setProprietaire($user); // Associer la voiture à l'utilisateur connecté

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($voiture);
            $entityManager->flush();

            $this->addFlash('success', 'Votre voiture a été enregistrée avec succès !');

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER); // Rediriger vers la liste des voitures
        }

        return $this->render('voiture/new.html.twig', [ // On va créer un nouveau template pour le formulaire
            'voiture' => $voiture,
            'form' => $form->createView(),
        ]);
    }

    // Ajouter ici des méthodes pour voir les détails d'une voiture, la modifier, la supprimer.
}
