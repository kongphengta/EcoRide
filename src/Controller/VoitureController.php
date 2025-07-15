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

#[Route('/voiture')]
class VoitureController extends AbstractController
{
    #[Route('', name: 'app_voiture_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // seul un utilisateur connecté peut voir ses voitures
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer les voitures de l'utilisateur connecté
        $voitures = $entityManager
            ->getRepository(Voiture::class)
            ->findBy(['proprietaire' => $this->getUser()]);

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
        $voiture->setProprietaire($this->getUser()); // Associer la voiture à l'utilisateur connecté

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

    #[Route('/{id}/modifier', name: 'app_voiture_modifier', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function modifierVoiture(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le propriétaire de la voiture
        if ($this->getUser() !== $voiture->getProprietaire()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette voiture.');
        }

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Les informations de votre voiture ont été mises à jour.');

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_voiture_supprimer', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function supprimerVoiture(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le propriétaire de la voiture
        if ($this->getUser() !== $voiture->getProprietaire()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette voiture.');
        }

        if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $request->request->get('_token'))) {
            $entityManager->remove($voiture);
            $entityManager->flush();
            $this->addFlash('success', 'Votre voiture a été supprimée.');
        }

        return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
    }
}
