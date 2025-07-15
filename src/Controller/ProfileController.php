<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/profil')]
#[IsGranted('ROLE_USER')] // Sécurise toutes les routes de ce contrôleur
class ProfileController extends AbstractController
{
    /**
     * Affiche la page principale du profil de l'utilisateur.
     */
    #[Route('', name: 'app_profile')]
    public function index(): Response
    {
        // Le getUser() récupère l'utilisateur actuellement connecté
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * Permet à l'utilisateur de modifier ses informations personnelles.
     */
    #[Route('/modifier', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Nous réutilisons ProfileFormType qui sert aussi à compléter le profil
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'profileForm' => $form->createView(),
        ]);
    }

    /**
     * Permet à l'utilisateur de changer son mot de passe.
     */
    #[Route('/changer-mot-de-passe', name: 'app_profile_change_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Nous supposons l'existence d'un ChangePasswordFormType
        // Si ce n'est pas le cas, il faudra le créer.
        // Il devrait contenir les champs : currentPassword, et newPassword (repeated)
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();

            // Hasher et définir le nouveau mot de passe
            $user->setPassword(
                $passwordHasher->hashPassword($user, $newPassword)
            );
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }

    /**
     * Affiche les covoiturages proposés par l'utilisateur.
     */
    #[Route('/mes-covoiturages', name: 'app_profile_my_covoiturages')]
    public function myCovoiturages(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // La relation $user->covoiturages est définie dans l'entité User
        $covoiturages = $user->getCovoiturages();

        return $this->render('profile/my_covoiturages.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }

    /**
     * Permet à un utilisateur de devenir chauffeur.
     */
    #[Route('/devenir-chauffeur', name: 'app_profile_become_driver', methods: ['POST'])]
    public function becomeDriver(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        // Sécurité : Vérifier le token CSRF
        if ($this->isCsrfTokenValid('become_driver' . $user->getId(), $request->request->get('_token'))) {
            // La méthode getRoles() mise à jour fonctionne toujours ici
            if (!in_array('ROLE_CHAUFFEUR', $user->getRoles(), true)) {
                // Récupérer le rôle "Chauffeur" depuis la base de données
                $roleChauffeur = $entityManager->getRepository(Role::class)->findOneBy(['libelle' => 'ROLE_CHAUFFEUR']);

                if ($roleChauffeur) {
                    $user->addEcoRideRole($roleChauffeur);
                    $entityManager->flush();
                    $this->addFlash('success', 'Félicitations ! Vous êtes maintenant enregistré comme chauffeur.');
                } else {
                    // Gérer le cas où le rôle n'existe pas, ce qui serait une erreur de configuration
                    $this->addFlash('danger', 'Une erreur de configuration est survenue. Le rôle chauffeur est introuvable.');
                }
            } else {
                $this->addFlash('info', 'Vous êtes déjà un chauffeur.');
            }
        }

        return $this->redirectToRoute('app_profile');
    }
}
