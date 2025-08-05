<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Entity\Reservation;
use App\Entity\Covoiturage;
use App\Entity\User;
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

    #[Route('/create/{id}', name: 'app_reservation_create', methods: ['POST'])]
    public function create(
        Request $request,
        Covoiturage $covoiturage,
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        // 1. Vérifications de sécurité
        if (!$this->isCsrfTokenValid('reserve_' . $covoiturage->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Erreur de sécurité. Veuillez réessayer.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 2. Vérifier que l'utilisateur n'est pas le conducteur
        if ($user === $covoiturage->getChauffeur()) {
            $this->addFlash('warning', 'Vous ne pouvez pas réserver votre propre trajet.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 3. Vérifier qu'il n'a pas déjà une réservation sur ce trajet
        $existingReservation = $entityManager->getRepository(Reservation::class)
            ->findOneBy([
                'covoiturage' => $covoiturage,
                'passager' => $user
            ]);

        if ($existingReservation) {
            $this->addFlash('info', 'Vous avez déjà une réservation sur ce trajet.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 4. Récupérer le nombre de places demandées
        $nbPlacesReservees = (int) $request->request->get('nb_places', 1);

        if ($nbPlacesReservees < 1 || $nbPlacesReservees > $covoiturage->getNbPlaceRestantes()) {
            $this->addFlash('danger', 'Nombre de places invalide.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 5. Vérifier que l'utilisateur a assez de crédits
        $prixTotal = $covoiturage->getPrixPersonne() * $nbPlacesReservees;
        if ($user->getCredits() < $prixTotal) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de crédits. Il vous faut ' . $prixTotal . ' crédits.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 6. Créer la réservation
        $reservation = new Reservation();
        $reservation->setCovoiturage($covoiturage);
        $reservation->setPassager($user);
        $reservation->setNbPlacesReservees($nbPlacesReservees);
        $reservation->setStatut('En attente');
        $reservation->setDateReservation(new \DateTimeImmutable());

        // 7. Débiter temporairement les crédits du passager (ils seront transférés au conducteur lors de la confirmation)
        $user->setCredits($user->getCredits() - $prixTotal);

        // 8. NE PAS mettre à jour les places restantes ici - cela se fera lors de la confirmation

        // 9. Sauvegarder
        $entityManager->persist($reservation);
        $entityManager->flush();

        // 10. Envoyer les notifications email
        $emailService->sendReservationCreatedEmail($reservation);
        $emailService->sendNewReservationToDriverEmail($reservation);

        $this->addFlash('success', 'Votre réservation a été créée avec succès ! Le conducteur va la valider.');

        return $this->redirectToRoute('app_profile_my_reservations');
    }

    #[Route('/{id}/confirm', name: 'app_reservation_confirm', methods: ['POST'])]
    public function confirm(
        Request $request,
        Reservation $reservation,
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response {
        $user = $this->getUser();

        // 1. Vérifier que l'utilisateur est bien le conducteur
        if ($user !== $reservation->getCovoiturage()->getChauffeur()) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // 2. Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('confirm_' . $reservation->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Erreur de sécurité.');
            return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $reservation->getCovoiturage()->getId()]);
        }

        // 3. Vérifier que la réservation est en attente
        if ($reservation->getStatut() !== 'En attente') {
            $this->addFlash('warning', 'Cette réservation a déjà été traitée.');
            return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $reservation->getCovoiturage()->getId()]);
        }

        // 4. Confirmer la réservation et transférer les crédits
        $reservation->setStatut('Confirmée');

        // Transférer les crédits du passager vers le conducteur
        $covoiturage = $reservation->getCovoiturage();
        $conducteur = $covoiturage->getChauffeur();
        $montantTransfert = $covoiturage->getPrixPersonne() * $reservation->getNbPlacesReservees();

        $conducteur->setCredits($conducteur->getCredits() + $montantTransfert);

        // Réduire définitivement les places restantes
        $covoiturage->setNbPlaceRestantes($covoiturage->getNbPlaceRestantes() - $reservation->getNbPlacesReservees());

        $entityManager->flush();

        // 5. Envoyer notification au passager
        try {
            $emailSent = $emailService->sendReservationConfirmedEmail($reservation);
            if ($emailSent) {
                $this->addFlash('info', 'Email de confirmation envoyé au passager.');
            } else {
                $this->addFlash('warning', 'Réservation confirmée mais l\'email n\'a pas pu être envoyé.');
            }
        } catch (\Exception $e) {
            $this->addFlash('warning', 'Réservation confirmée mais erreur email : ' . $e->getMessage());
        }

        $this->addFlash('success', 'Réservation confirmée avec succès !');

        return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $reservation->getCovoiturage()->getId()]);
    }

    #[Route('/{id}/reject', name: 'app_reservation_reject', methods: ['POST'])]
    public function reject(
        Request $request,
        Reservation $reservation,
        EntityManagerInterface $entityManager,
        EmailService $emailService
    ): Response {
        $user = $this->getUser();

        // 1. Vérifier que l'utilisateur est bien le conducteur
        if ($user !== $reservation->getCovoiturage()->getChauffeur()) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        // 2. Vérifier le token CSRF
        if (!$this->isCsrfTokenValid('reject_' . $reservation->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Erreur de sécurité.');
            return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $reservation->getCovoiturage()->getId()]);
        }

        // 3. Vérifier que la réservation est en attente
        if ($reservation->getStatut() !== 'En attente') {
            $this->addFlash('warning', 'Cette réservation a déjà été traitée.');
            return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $reservation->getCovoiturage()->getId()]);
        }

        $covoiturage = $reservation->getCovoiturage();

        // 4. Rembourser les crédits au passager
        $passager = $reservation->getPassager();
        $prixTotal = $covoiturage->getPrixPersonne() * $reservation->getNbPlacesReservees();
        $passager->setCredits($passager->getCredits() + $prixTotal);

        // 5. Remettre les places disponibles
        $covoiturage->setNbPlaceRestantes($covoiturage->getNbPlaceRestantes() + $reservation->getNbPlacesReservees());

        // 6. Marquer la réservation comme rejetée
        $reservation->setStatut('Rejetée');

        $entityManager->flush();

        // 7. Envoyer notification au passager
        $emailService->sendReservationRejectedEmail($reservation);

        $this->addFlash('success', 'Réservation rejetée. Le passager a été remboursé.');

        return $this->redirectToRoute('app_covoiturage_passengers', ['id' => $covoiturage->getId()]);
    }
}
