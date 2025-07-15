<?php
// src/Controller/CovoiturageController.php
namespace App\Controller;


use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use App\Repository\VoitureRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/covoiturage')]
class CovoiturageController extends AbstractController
{
    #[Route('/', name: 'app_covoiturage_index', methods: ['GET'])]
    public function index(Request $request, CovoiturageRepository $covoiturageRepository): Response
    {
        $depart = $request->query->get('depart');
        $arrivee = $request->query->get('arrivee');
        $date_str = $request->query->get('date');
        $date = null;
        if ($date_str) {
            try {
                $date = new \DateTimeImmutable($date_str);
            } catch (\Exception $e) {
                // Gérer l'erreur de format de date si nécessaire, ou laisser null
                $this->addFlash('warning', 'Le format de la date de recherche est invalide.');
            }
        }
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Covoiturages', 'url' => $this->generateUrl('app_covoiturage_index')],
        ];

        // Si au moins un critère de recherche est fourni, on utilise la méthode de recherche.
        if ($depart || $arrivee || $date) {
            $covoiturages = $covoiturageRepository->searchCovoiturages($depart, $arrivee, $date);
            $breadcrumb[] = ['label' => 'Résultats de recherche', 'url' => $this->generateUrl('app_covoiturage_index', $request->query->all())];
        } else {
            // si non, on affiche tous les covoiturages à venir, triés par date.
            $covoiturages = $covoiturageRepository->findUpcoming('ASC');
        }
        return $this->render('covoiturage/index.html.twig', [
            'breadcrumb' => $breadcrumb,
            'covoiturages' => $covoiturages,
            'search_params' => [
                'depart' => $depart,
                'arrivee' => $arrivee,
                'date' => $date_str,
            ],
        ]);
    }
    #[Route('/new', name: 'app_covoiturage_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, VoitureRepository $voitureRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CHAUFFEUR'); // Vérifie si l'utilisateur a le rôle de chauffeur
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Vérifier si l'utilisateur (chauffeur)a au moins une voiture enregistrée
        $userVoitures = $voitureRepository->findBy(['proprietaire' => $user]);
        // Si l'utilisateur n'a pas de voiture, rediriger vers la page d'ajout de voiture
        if (empty($userVoitures)) {
            $this->addFlash('error', 'Vous devez d\'abord enregistrer une voiture avant de proposer un covoiturage.');
            return $this->redirectToRoute('app_voiture_ajouter');
        }
        $covoiturage = new Covoiturage();
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

    #[Route('/{id}/edit', name: 'app_covoiturage_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CHAUFFEUR')]
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
        ]);
    }

    #[Route('/{id}', name: 'app_covoiturage_delete', methods: ['POST'])]
    #[IsGranted('ROLE_CHAUFFEUR')]
    public function delete(Request $request, Covoiturage $covoiturage, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le chauffeur du covoiturage
        if ($this->getUser() !== $covoiturage->getChauffeur()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce covoiturage.');
        }

        // Sécurité : Vérifier le token CSRF pour se protéger contre les attaques
        if ($this->isCsrfTokenValid('delete' . $covoiturage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($covoiturage);
            $entityManager->flush();

            $this->addFlash('success', 'Le covoiturage a été supprimé.');
        }

        return $this->redirectToRoute('app_profile_my_covoiturages', [], Response::HTTP_SEE_OTHER);
    }
}
