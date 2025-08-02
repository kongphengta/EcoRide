<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reservation')]
#[IsGranted('ROLE_USER')]
class ReservationController extends AbstractController
{
    #[Route('/{id}/annuler', name: 'app_reservation_cancel', methods: ['POST'])]
    public function cancel(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // 1. Sécurité : Vérifier que l'utilisateur connecté est bien le passager de la réservation
        if ($this->getUser() !== $reservation->getPassager()) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // 2. Sécurité : Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('cancel' . $reservation->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'La tentative d\'annulation a échoué en raison d\'un problème de sécurité.');
            return $this->redirectToRoute('app_profile_my_reservations');
        }

        // 3. Logique métier : On ne peut annuler qu'une réservation qui n'est pas déjà annulée
        if ($reservation->getStatut() === 'Confirmée' || $reservation->getStatut() === 'En attente') {
            // Mettre à jour le statut de la réservation
            $reservation->setStatut('Annulée');

            // Recréditer la place dans le covoiturage associé
            $covoiturage = $reservation->getCovoiturage();
            $covoiturage->setNbPlaceRestantes($covoiturage->getNbPlaceRestantes() + $reservation->getNbPlacesReservees());

            // US 9: Rembourser les crédits au passager
            $passager = $reservation->getPassager();
            $prixTrajet = $covoiturage->getPrixPersonne();
            $passager->setCredits($passager->getCredits() + ($prixTrajet * $reservation->getNbPlacesReservees()));

            $entityManager->flush();
            $this->addFlash('success', 'Votre réservation a bien été annulée.');
        } else {
            $this->addFlash('warning', 'Cette réservation ne peut plus être annulée.');
        }

        return $this->redirectToRoute('app_profile_my_reservations', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/chauffeur-annuler', name: 'app_reservation_driver_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_CHAUFFEUR')]
    public function driverCancel(Request $request, Reservation $reservation, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        $covoiturage = $reservation->getCovoiturage();

        // 1. Sécurité : Vérifier que l'utilisateur connecté est bien le chauffeur du covoiturage
        if ($this->getUser() !== $covoiturage->getChauffeur()) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // 2. Sécurité : Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('driver_cancel' . $reservation->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'La tentative d\'annulation a échoué en raison d\'un problème de sécurité.');
            return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $covoiturage->getId()]);
        }

        // 3. Logique métier : On ne peut annuler qu'une réservation qui est 'Confirmée'
        if ($reservation->getStatut() === 'Confirmée') {
            // Mettre à jour le statut de la réservation avec une mention spéciale
            $reservation->setStatut('Annulée par le chauffeur');

            // Recréditer la place dans le covoiturage associé
            $covoiturage->setNbPlaceRestantes($covoiturage->getNbPlaceRestantes() + $reservation->getNbPlacesReservees());

            // US 9: Rembourser les crédits au passager
            $passager = $reservation->getPassager();
            $prixTrajet = $covoiturage->getPrixPersonne();
            $passager->setCredits($passager->getCredits() + ($prixTrajet * $reservation->getNbPlacesReservees()));

            $entityManager->flush();
            $this->addFlash('success', 'La réservation du passager a bien été annulée.');

            // Envoyer l'email de notification au passager
            $emailService->sendReservationCancelledByDriverEmail($reservation);
        } else {
            $this->addFlash('warning', 'Cette réservation ne peut plus être annulée.');
        }

        return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $covoiturage->getId()], Response::HTTP_SEE_OTHER);
    }
}
