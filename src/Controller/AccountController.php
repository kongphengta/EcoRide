<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/account')]
#[IsGranted('ROLE_USER')]
class AccountController extends AbstractController
{
    #[Route('/espace-employe', name: 'app_profile_employe')]
    #[IsGranted('ROLE_EMPLOYE', message: 'Vous devez être employé pour accéder à cette page.', statusCode: 403)]
    public function employe(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $reservations = $user->getReservations();

        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Mon Profil', 'url' => $this->generateUrl('app_profile')],
            ['label' => 'Espace Employé', 'url' => $this->generateUrl('app_profile_employe')],
        ];

        return $this->render('profile/employe.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/devenir-employe', name: 'app_profile_become_employe')]
    public function becomeEmploye(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $roleRepository = $entityManager->getRepository(\App\Entity\Role::class);
        $employeRole = $roleRepository->findOneBy(['libelle' => 'ROLE_EMPLOYE']);
        
        if ($employeRole && !in_array('ROLE_EMPLOYE', $user->getRoles())) {
            $user->addEcoRideRole($employeRole);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes maintenant employé !');
        } else {
            $this->addFlash('info', 'Vous êtes déjà employé.');
        }

        return $this->redirectToRoute('app_profile');
    }
}
