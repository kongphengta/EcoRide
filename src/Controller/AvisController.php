<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reservation;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/avis')]
#[IsGranted('ROLE_USER')]
class AvisController extends AbstractController
{
    #[Route('/new/{id}', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        // 1. Sécurité et logique métier
        // L'utilisateur connecté doit être le passager de la réservation
        if ($this->getUser() !== $reservation->getPassager()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas laisser d\'avis pour ce trajet.');
        }

        // Le covoiturage doit être 'Terminé'
        if ($reservation->getCovoiturage()->getStatut() !== 'Terminé') {
            $this->addFlash('warning', 'Vous ne pouvez laisser un avis qu\'après la fin du trajet.');
            return $this->redirectToRoute('app_profile_my_reservations');
        }

        // Un seul avis par réservation (nécessite une relation entre Avis et Reservation)
        if ($reservation->getAvis()) {
            $this->addFlash('info', 'Vous avez déjà laissé un avis pour ce trajet.');
            return $this->redirectToRoute('app_profile_my_reservations');
        }

        $avis = new Avis();
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 2. Hydrater l'entité Avis
            $avis->setAuteur($this->getUser()); // Le passager
            $avis->setReceveur($reservation->getCovoiturage()->getChauffeur()); // Le chauffeur
            $avis->setReservation($reservation); // Lier l'avis à la réservation
            // La date de publication est déjà définie dans le constructeur de l'entité Avis.
            // $avis->setDateCreation(new \DateTimeImmutable()); // Cette ligne est donc redondante.

            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash('success', 'Votre avis a été publié. Merci pour votre contribution !');

            return $this->redirectToRoute('app_profile_my_reservations');
        }

        return $this->render('avis/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }
}
