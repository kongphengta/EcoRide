<?php
// src/Controller/CovoiturageController.php
namespace App\Controller;


use App\Entity\Covoiturage;
use App\Entity\Voiture;
use App\Form\CovoiturageType;
use App\Repository\VoitureRepository;
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
    public function index(Request $request, EntityManagerInterface $entityManager): Response
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
        $covoiturageRepository = $entityManager->getRepository(Covoiturage::class);

        // if ($depart && $arrivee && $date) { // Condition trop restrictive
        if ($depart || $arrivee || $date) { // Si au moins un critère est fourni
            // Assurez-vous que la méthode s'appelle bien searchCovoiturages et qu'elle existe dans votre Repository
            // J'utilise 'searchCovoiturages' comme dans ma suggestion précédente. Si c'est 'searchCovoiturage', ajustez.
            $covoiturages = $covoiturageRepository->searchCovoiturages($depart, $arrivee, $date);
            $breadcrumb[] = ['label' => 'Résultats de recherche', 'url' => $this->generateUrl('app_covoiturage_index', $request->query->all())];
        } else {
            // Si aucun critère n'est fourni, récupérer tous les covoiturages, triés par date de départ
            // $covoiturages = $covoiturageRepository->findAll(['dateDepart' => 'ASC']); // findAll n'accepte pas de paramètres de tri comme ça
            $covoiturages = $covoiturageRepository->findBy([], ['dateDepart' => 'ASC']);
            // $breadcrumb[] = ['label' => 'Tous les covoiturages', 'url' => $this->generateUrl('app_covoiturage_index')]; // Optionnel, déjà dans le breadcrumb initial
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
            $user = $this->getUser();
            $covoiturage->setChauffeur($user);

            // Définir le statut par défaut si non fourni par le formulaire
            if (!$covoiturage->getStatut()) {
                $covoiturage->setStatut('Proposé');
            }
            // Enregistrer le covoiturage dans la base de données
            $entityManager->persist($covoiturage);
            $entityManager->flush();
            $this->addFlash('success', 'Covoiturage a été publié avec succès !');

            // Rediriger vers la liste des trajets ou le détails du trajet créé
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
    #[Route('/profile/become-driver', name: 'app_profile_become_driver', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function becomeDriver(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        // Sécurité : Vérifier le token CSRF
        if ($this->isCsrfTokenValid('become_driver' . $user->getId(), $request->request->get('_token'))) {
            if (!in_array('ROLE_CHAUFFEUR', $user->getRoles(), true)) {
                $roles = $user->getRoles();
                // Ajouter le rôle chauffeur
                $roles[] = 'ROLE_CHAUFFEUR';
                $user->setRoles(array_unique($roles)); // Pour éviter les doublons
                // Mettre à jour l'utilisateur dans la base de données
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', ' Félicitations ! Vous êtes maintenant enregistré comme chauffeur.');
            } else {
                // Si l'utilisateur a déjà le rôle chauffeur, on peut choisir de rediriger ou d'afficher un message
                $this->addFlash('info', 'Vous êtes déjà un chauffeur.');
            }
        } else {
            // Si le token CSRF n'est pas valide, on peut choisir de rediriger ou d'afficher un message
            $this->addFlash('error', 'Requête invalide pour devenir chauffeur.');
        }
        return $this->redirectToRoute('app_profile');
    }
}
