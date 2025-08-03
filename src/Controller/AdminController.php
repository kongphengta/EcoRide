<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{

    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(CovoiturageRepository $covoiturageRepo, UserRepository $userRepo): Response
    {
        // Récupérer le nombre de covoiturages par jour
        $stats = $covoiturageRepo->createQueryBuilder('c')
            ->select('c.dateDepart AS dateDepart', 'COUNT(c.id) as total')
            ->groupBy('c.dateDepart')
            ->orderBy('c.dateDepart', 'ASC')
            ->getQuery()
            ->getResult();

        // Crédits gagnés par jour
        $jours = [];
        $totaux = [];
        foreach ($stats as $row) {
            $jours[] = $row['dateDepart'] instanceof \DateTimeInterface ? $row['dateDepart']->format('Y-m-d') : $row['dateDepart'];
            $totaux[] = $row['total'];
        }
        $covoiturages = $covoiturageRepo->createQueryBuilder('c')
            ->select('c.dateDepart', 'c.prixPersonne', 'c.nbPlaceTotal', 'c.nbPlaceRestantes')
            ->orderBy('c.dateDepart', 'ASC')
            ->getQuery()
            ->getResult();

        $creditsParJour = [];
        foreach ($covoiturages as $covoit) {
            $jour = $covoit['dateDepart'] instanceof \DateTimeInterface ? $covoit['dateDepart']->format('Y-m-d') : $covoit['dateDepart'];
            $placesVendues = $covoit['nbPlaceTotal'] - $covoit['nbPlaceRestantes'];
            $gain = $covoit['prixPersonne'] * $placesVendues;
            if (!isset($creditsParJour[$jour])) {
                $creditsParJour[$jour] = 0;
            }
            $creditsParJour[$jour] += $gain;
        }

        $joursCredits = array_keys($creditsParJour);
        $creditsParJourValues = array_values($creditsParJour);
        $totalCredits = array_sum($creditsParJourValues);

        $users = $userRepo->findAll();

        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Administration', 'url' => $this->generateUrl('admin_dashboard')],
        ];

        return $this->render('admin/dashboard.html.twig', [
            'jours' => $jours,
            'totaux' => $totaux,
            'joursCredits' => $joursCredits,
            'creditsParJour' => $creditsParJourValues,
            'totalCredits' => $totalCredits,
            'users' => $users,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/creer-employe', name: 'admin_create_employe')]
    public function createEmploye(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Ajout du rôle employé
            $roleRepo = $entityManager->getRepository(Role::class);
            $employeRole = $roleRepo->findOneBy(['libelle' => 'ROLE_EMPLOYE']);
            $user->addEcoRideRole($employeRole);

            // Récupération du mot de passe depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Employé créé avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/create_employe.html.twig', [
            'form' => $form->createView(),
            'breadcrumb' => [
                ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
                ['label' => 'Administration', 'url' => $this->generateUrl('admin_dashboard')],
                ['label' => 'Créer un employé', 'url' => $this->generateUrl('admin_create_employe')],
            ],
        ]);
    }
    #[Route('/suspendre-utilisateur/{id}', name: 'admin_suspend_user')]
    public function suspendUser(User $user, EntityManagerInterface $em): Response
    {
        $user->setIsActive(false);
        $em->flush();
        $this->addFlash('success', 'Utilisateur suspendu.');
        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/reactiver-utilisateur/{id}', name: 'admin_activate_user')]
    public function activateUser(User $user, EntityManagerInterface $em): Response
    {
        $user->setIsActive(true);
        $em->flush();
        $this->addFlash('success', 'Utilisateur réactivé.');
        return $this->redirectToRoute('admin_dashboard');
    }
}
