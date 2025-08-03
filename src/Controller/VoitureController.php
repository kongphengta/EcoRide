<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/voiture')]
#[IsGranted('ROLE_USER')]
class VoitureController extends AbstractController
{
    #[Route('/', name: 'app_voiture_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $voitures = $user->getVoitures();

        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Mon Profil', 'url' => $this->generateUrl('app_profile')],
            ['label' => 'Mes Voitures', 'url' => $this->generateUrl('app_voiture_index')],
        ];

        return $this->render('voiture/index.html.twig', [
            'voitures' => $voitures,
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/new', name: 'app_voiture_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RoleRepository $roleRepository): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $this->getUser();
            $voiture->setProprietaire($user);

            // Automatically grant driver role if the user doesn't have it yet.
            if (!in_array('ROLE_CHAUFFEUR', $user->getRoles())) {
                $chauffeurRole = $roleRepository->findOneBy(['libelle' => 'ROLE_CHAUFFEUR']);
                if ($chauffeurRole) {
                    $user->addEcoRideRole($chauffeurRole);
                    $this->addFlash('info', 'Félicitations ! Vous êtes maintenant chauffeur et pouvez proposer des trajets.');
                }
            }

            $entityManager->persist($voiture);
            $entityManager->flush();

            $this->addFlash('success', 'Votre voiture a été ajoutée avec succès !');

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture/new.html.twig', [
            'voiture' => $voiture,
            'form' => $form->createView(),
            'breadcrumb' => [
                ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
                ['label' => 'Mon Profil', 'url' => $this->generateUrl('app_profile')],
                ['label' => 'Mes Voitures', 'url' => $this->generateUrl('app_voiture_index')],
                ['label' => 'Ajouter une voiture', 'url' => $this->generateUrl('app_voiture_new')],
            ],
        ]);
    }

    #[Route('/{id}/edit', name: 'app_voiture_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Voiture $voiture, EntityManagerInterface $entityManager): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le propriétaire de la voiture
        if ($voiture->getProprietaire() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Les informations de la voiture ont été mises à jour.');

            return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voiture/edit.html.twig', [
            'voiture' => $voiture,
            'form' => $form->createView(),
            'breadcrumb' => [
                ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
                ['label' => 'Mon Profil', 'url' => $this->generateUrl('app_profile')],
                ['label' => 'Mes Voitures', 'url' => $this->generateUrl('app_voiture_index')],
                ['label' => 'Modifier une voiture', 'url' => ''],
            ],
        ]);
    }

    #[Route('/{id}', name: 'app_voiture_delete', methods: ['POST'])]
    public function delete(Request $request, Voiture $voiture, EntityManagerInterface $entityManager, RoleRepository $roleRepository): Response
    {
        // Sécurité : Vérifier que l'utilisateur connecté est bien le propriétaire de la voiture
        if ($voiture->getProprietaire() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Action non autorisée.');
        }

        if ($this->isCsrfTokenValid('delete' . $voiture->getId(), $request->request->get('_token'))) {
            /** @var User $user */
            $user = $this->getUser();

            $entityManager->remove($voiture);
            $entityManager->flush();

            // If the user has no more cars, remove the driver role
            if ($user->getVoitures()->isEmpty() && in_array('ROLE_CHAUFFEUR', $user->getRoles())) {
                $chauffeurRole = $roleRepository->findOneBy(['libelle' => 'ROLE_CHAUFFEUR']);
                if ($chauffeurRole) {
                    $user->removeEcoRideRole($chauffeurRole);
                    $entityManager->flush();
                    $this->addFlash('info', 'Vous n\'avez plus de voiture, votre rôle de chauffeur a été retiré.');
                }
            }

            $this->addFlash('success', 'La voiture a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_voiture_index', [], Response::HTTP_SEE_OTHER);
    }
}
