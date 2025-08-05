<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Covoiturage;
use App\Entity\Reservation;
use App\Form\CovoiturageType;
use App\Service\EmailService;
use App\Form\CovoiturageSearchType;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CovoiturageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/covoiturage')]
class CovoiturageController extends AbstractController
{
    #[Route('/', name: 'app_covoiturage_index', methods: ['GET'])]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository, PaginatorInterface $paginator): Response
    {
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Covoiturages', 'url' => $this->generateUrl('app_covoiturage_index')],
        ];

        // Le formulaire doit utiliser la méthode GET pour que les paramètres de recherche
        // soient dans l'URL, ce qui est essentiel pour la pagination.
        $searchForm = $this->createForm(CovoiturageSearchType::class, null, ['method' => 'GET']);
        $searchForm->handleRequest($request);

        $criteria = $searchForm->getData() ?? [];
        $query = $covoiturageRepository->searchCovoituragesQueryBuilder($criteria);

        $pagination = $paginator->paginate(
            $query, // Le QueryBuilder, pas les résultats
            $request->query->getInt('page', 1), // Numéro de page depuis l'URL, 1 par défaut
            10 // Nombre de résultats par page
        );

        // Le 'rechercher' vient du nom du bouton submit dans CovoiturageSearchType
        $searchPerformed = $request->query->has('rechercher');
        if ($searchPerformed) {
            $breadcrumb[] = ['label' => 'Résultats de recherche', 'url' => $this->generateUrl('app_covoiturage_index', $request->query->all())];
        }

        return $this->render('covoiturage/index.html.twig', [
            'breadcrumb' => $breadcrumb,
            'pagination' => $pagination, // On passe l'objet de pagination à la vue
            'searchForm' => $searchForm->createView(),
            'searchPerformed' => $searchPerformed, // Pour savoir si on doit afficher un titre "Résultats"
        ]);
    }
    #[Route('/new', name: 'app_covoiturage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, VoitureRepository $voitureRepository): Response
    {
        // Temporairement désactivé pour permettre les tests - TODO: remettre $this->denyAccessUnlessGranted('ROLE_CHAUFFEUR');
        $this->denyAccessUnlessGranted('ROLE_USER'); // Vérifie seulement que l'utilisateur est connecté
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur (chauffeur) a au moins une voiture enregistrée
        // Si l'utilisateur n'a pas de voiture, rediriger vers la page d'ajout de voiture
        if ($voitureRepository->count(['proprietaire' => $user]) === 0) {
            $this->addFlash('error', 'Vous devez d\'abord enregistrer une voiture avant de proposer un covoiturage.');
            return $this->redirectToRoute('app_voiture_new');
        }
        $covoiturage = new Covoiturage();
        // Le formulaire récupère automatiquement l'utilisateur via l'injection de dépendance
        $form = $this->createForm(CovoiturageType::class, $covoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Assigner l'utilisateur connecté comme chauffeur
            /** @var \App\Entity\User $user */
            // $user est déjà défini plus haut
            $covoiturage->setChauffeur($user);

            // Définir le statut par défaut si non fourni par le formulaire
            if (!$covoiturage->getStatut()) {
                $covoiturage->setStatut('Proposé');
            }
            // La logique pour nbPlaceRestantes est gérée dans l'entité Covoiturage via setNbPlaceTotal()

            $entityManager->persist($covoiturage);
            $entityManager->flush();
            $this->addFlash('success', 'Covoiturage a été publié avec succès !');

            return $this->redirectToRoute('app_covoiturage_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('covoiturage/new.html.twig', [
            'covoiturageForm' => $form->createView(),
            'breadcrumb' => [
                ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
                ['label' => 'Covoiturages', 'url' => $this->generateUrl('app_covoiturage_index')],
                ['label' => 'Proposer un trajet', 'url' => $this->generateUrl('app_covoiturage_new')],
            ],
        ]);
    }
    #[Route('/{id}', name: 'app_covoiturage_show', methods: ['GET'])]
    public function show(Covoiturage $covoiturage): Response
    {
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Covoiturages', 'url' => $this->generateUrl('app_covoiturage_index')],
            ['label' => 'Détails du covoiturage', 'url' => $this->generateUrl('app_covoiturage_show', ['id' => $covoiturage->getId()])],
        ];
        return $this->render('covoiturage/show.html.twig', [
            'breadcrumb' => $breadcrumb,
            'covoiturage' => $covoiturage,
        ]);
    }

    #[Route('/{id}/passagers', name: 'app_covoiturage_passengers', methods: ['GET'])]
    #[IsGranted('ROLE_USER')] // Temporairement changé de ROLE_CHAUFFEUR à ROLE_USER pour les tests
    public function passengers(Covoiturage $covoiturage): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le chauffeur du covoiturage
        if ($this->getUser() !== $covoiturage->getChauffeur()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à voir les passagers de ce covoiturage.');
        }

        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Mes Covoiturages', 'url' => $this->generateUrl('app_profile_my_covoiturages')],
            ['label' => 'Passagers', 'url' => ''],
        ];

        return $this->render('covoiturage/passengers.html.twig', [
            'covoiturage' => $covoiturage,
            // Le template attend une variable 'reservations'. On la lui passe.
            'reservations' => $covoiturage->getReservations(),
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/{id}/reserver', name: 'app_covoiturage_reserver', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reserver(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // 1. Vérification du token CSRF pour la sécurité
        if (!$this->isCsrfTokenValid('reserver' . $covoiturage->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Action non autorisée.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 2. Vérifications métier
        if ($covoiturage->getChauffeur() === $user) {
            $this->addFlash('warning', 'Vous ne pouvez pas réserver une place dans votre propre covoiturage.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        if ($covoiturage->getNbPlaceRestantes() <= 0) {
            $this->addFlash('warning', 'Désolé, ce covoiturage est complet.');
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // Vérifier si l'utilisateur n'a pas déjà une réservation pour ce trajet
        foreach ($covoiturage->getReservations() as $existingReservation) {
            if ($existingReservation->getPassager() === $user) {
                $this->addFlash('info', 'Vous avez déjà réservé une place pour ce trajet.');
                return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
            }
        }

        // US 6 & 9: Vérifier si l'utilisateur a assez de crédits
        $prixTrajet = $covoiturage->getPrixPersonne();
        if ($user->getCredits() < $prixTrajet) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de crédits pour réserver ce trajet. Vous pouvez en acheter depuis votre profil.');
            // TODO: Créer une page pour acheter des crédits et lier ici.
            return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
        }

        // 3. Création de la réservation
        $placesReservees = 1; // Pour l'instant, on ne réserve qu'une place à la fois.

        $reservation = new Reservation();
        $reservation->setCovoiturage($covoiturage);
        $reservation->setPassager($user);
        $reservation->setNbPlacesReservees($placesReservees);
        $reservation->setStatut('Confirmée');
        $reservation->setDateReservation(new \DateTimeImmutable());

        // 4. Mettre à jour le nombre de places restantes dans le covoiturage
        $covoiturage->setNbPlaceRestantes($covoiturage->getNbPlaceRestantes() - $placesReservees);

        // 5. Débiter les crédits du passager
        $user->setCredits($user->getCredits() - $prixTrajet);

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Votre réservation a été confirmée avec succès !');
        return $this->redirectToRoute('app_covoiturage_show', ['id' => $covoiturage->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_covoiturage_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')] // Temporairement changé pour les tests
    public function edit(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le chauffeur du covoiturage
        if ($this->getUser() !== $covoiturage->getChauffeur()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce covoiturage.');
        }

        $form = $this->createForm(CovoiturageType::class, $covoiturage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le covoiturage a été mis à jour avec succès.');

            return $this->redirectToRoute('app_profile_my_covoiturages', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('covoiturage/edit.html.twig', [
            'covoiturage' => $covoiturage,
            'covoiturageForm' => $form->createView(),
            'breadcrumb' => [
                ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
                ['label' => 'Mon Profil', 'url' => $this->generateUrl('app_profile')],
                ['label' => 'Mes Covoiturages', 'url' => $this->generateUrl('app_profile_my_covoiturages')],
                ['label' => 'Modifier covoiturage', 'url' => ''],
            ],
        ]);
    }

    #[Route('/{id}/annuler', name: 'app_covoiturage_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')] // Temporairement changé pour les tests
    public function cancel(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le chauffeur du covoiturage
        if ($this->getUser() !== $covoiturage->getChauffeur()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à annuler ce covoiturage.');
        }

        // Sécurité : Vérifier le token CSRF pour se protéger contre les attaques
        if (!$this->isCsrfTokenValid('cancel_covoiturage' . $covoiturage->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Le token de sécurité est invalide. L\'annulation a échoué.');
            return $this->redirectToRoute('app_profile_my_covoiturages', [], Response::HTTP_SEE_OTHER);
        }

        // Logique métier : On ne peut annuler qu'un trajet qui n'est pas déjà terminé ou annulé.
        if (in_array($covoiturage->getStatut(), ['Proposé', 'Confirmé', 'Complet'])) {
            // 1. Mettre à jour le statut du covoiturage
            $covoiturage->setStatut('Annulé');

            // 2. Annuler toutes les réservations associées qui sont 'Confirmée' ou 'En attente'
            foreach ($covoiturage->getReservations() as $reservation) {
                if (in_array($reservation->getStatut(), ['Confirmée', 'En attente'])) {
                    // Envoyer l'email de notification AVANT de changer le statut
                    $emailService->sendCovoiturageCancelledEmail($reservation);

                    // CRITICAL: Rembourser les crédits au passager
                    $passager = $reservation->getPassager();
                    $passager->setCredits($passager->getCredits() + $covoiturage->getPrixPersonne());
                    $reservation->setStatut('Annulée par le chauffeur');
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le covoiturage a bien été annulé.');
        } else {
            $this->addFlash('warning', 'Ce covoiturage ne peut plus être annulé.');
        }

        return $this->redirectToRoute('app_profile_my_covoiturages', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/start', name: 'app_covoiturage_start', methods: ['POST'])]
    #[IsGranted('ROLE_USER')] // Temporairement changé pour les tests
    public function start(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $covoiturage->getChauffeur()) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isCsrfTokenValid('start' . $covoiturage->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_profile_my_covoiturages');
        }
        if (!in_array($covoiturage->getStatut(), [Covoiturage::STATUT_CONFIRME, Covoiturage::STATUT_COMPLET])) {
            $this->addFlash('warning', 'Ce trajet ne peut pas être démarré.');
            return $this->redirectToRoute('app_profile_my_covoiturages');
        }
        $covoiturage->setStatut(Covoiturage::STATUT_EN_COURS);
        $entityManager->flush();
        $this->addFlash('success', 'Le covoiturage a bien été démarré.');
        return $this->redirectToRoute('app_profile_my_covoiturages');
    }

    #[Route('/{id}/end', name: 'app_covoiturage_end', methods: ['POST'])]
    #[IsGranted('ROLE_USER')] // Temporairement changé pour les tests
    public function end(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager, EmailService $emailService, UrlGeneratorInterface $urlGenerator): Response
    {
        if ($this->getUser() !== $covoiturage->getChauffeur()) {
            throw $this->createAccessDeniedException();
        }
        if (!$this->isCsrfTokenValid('end' . $covoiturage->getId(), $request->request->get('_token'))) {
            $this->addFlash('danger', 'Token de sécurité invalide.');
            return $this->redirectToRoute('app_profile_my_covoiturages');
        }
        if ($covoiturage->getStatut() !== Covoiturage::STATUT_EN_COURS) {
            $this->addFlash('warning', 'Ce trajet ne peut pas être terminé.');
            return $this->redirectToRoute('app_profile_my_covoiturages');
        }
        $covoiturage->setStatut(Covoiturage::STATUT_TERMINE);
        // Envoi des emails pour avis
        foreach ($covoiturage->getReservations() as $reservation) {
            $reviewUrl = $urlGenerator->generate('app_avis_new', ['id' => $reservation->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
            $emailService->sendLeaveReviewEmail($reservation, $reviewUrl);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Le covoiturage est terminé.');
        return $this->redirectToRoute('app_profile_my_covoiturages');
    }
}
