<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CovoiturageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Psr\Log\LoggerInterface;

#[Route('/profil')]
#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/mes-covoiturages', name: 'app_profile_my_covoiturages')]
    public function myCovoiturages(CovoiturageRepository $covoiturageRepository): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer les covoiturages où l'utilisateur est le chauffeur
        $covoiturages = $covoiturageRepository->findBy(
            ['chauffeur' => $user],
            ['dateDepart' => 'DESC'] // Trier par date de départ, les plus récents en premier
        );

        return $this->render('profile/my_covoiturages.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }

    #[Route('/', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/profile.html.twig', [
            'user' => $this->getUser(),
            'changePasswordForm' => $this->createForm(ChangePasswordFormType::class)->createView(),  // ← AJOUTER CETTE LIGNE
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, LoggerInterface $logger): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $logger->info("ProfileController: DEBUT edit()", ['userId' => $user->getId(), 'method' => $request->getMethod()]);

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        $logger->info("ProfileController: Après handleRequest", [
            'isSubmitted' => $form->isSubmitted(),
            'isValid' => $form->isSubmitted() ? $form->isValid() : 'N/A'
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $logger->info("ProfileController: Formulaire soumis et valide");

            // Gérer l'upload de la photo
            $photoFile = $form->get('photoFile')->getData();

            $logger->info("ProfileController: PhotoFile récupéré", [
                'hasFile' => $photoFile !== null,
                'filename' => $photoFile ? $photoFile->getClientOriginalName() : 'N/A',
                'size' => $photoFile ? $photoFile->getSize() : 'N/A'
            ]);

            if ($photoFile) {
                try {
                    $newFilename = $fileUploader->upload($photoFile);
                    $logger->info("ProfileController: Upload réussi", ['newFilename' => $newFilename]);

                    $user->setPhoto($newFilename);
                    $logger->info("ProfileController: Photo définie sur user", [
                        'photo' => $user->getPhoto(),
                        'userId' => $user->getId()
                    ]);
                } catch (\Exception $e) {
                    $logger->error("ProfileController: Erreur upload", ['error' => $e->getMessage()]);
                    $this->addFlash('error', 'Erreur lors de l\'upload de la photo: ' . $e->getMessage());
                    return $this->redirectToRoute('app_profile_edit');
                }
            } else {
                $logger->info("ProfileController: Pas de fichier photo uploadé");
            }

            $logger->info("ProfileController: Avant flush", [
                'userPhoto' => $user->getPhoto(),
                'userId' => $user->getId(),
                'userEmail' => $user->getEmail()
            ]);

            $entityManager->flush();

            $logger->info("ProfileController: Après flush - Vérification immédiate", ['userPhoto' => $user->getPhoto()]);

            $this->addFlash('success', 'Profil mis à jour avec succès !');
            return $this->redirectToRoute('app_profile');
        } else {
            if ($form->isSubmitted()) {
                $logger->warning("ProfileController: Formulaire soumis mais INVALIDE");
                foreach ($form->getErrors(true) as $error) {
                    $logger->warning("ProfileController: Erreur formulaire", ['error' => $error->getMessage()]);
                }
            } else {
                $logger->info("ProfileController: Formulaire PAS soumis - affichage initial");
            }
        }

        $logger->info("ProfileController: FIN edit() - Affichage template", ['userId' => $user->getId()]);
        return $this->render('profile/edit_profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    #[Route('/change-password', name: 'change_password')]
    #[IsGranted('ROLE_USER')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }
        $form = $this->createForm(ChangePasswordFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('newPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe modifié avec succès !');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/devenir-chauffeur', name: 'app_profile_become_driver')]
    public function becomeDriver(EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        $user->setIsChauffeur(true); // Assurez-vous que le champ existe dans l'entité User
        $entityManager->flush();

        $this->addFlash('success', 'Vous êtes maintenant chauffeur !');
        return $this->redirectToRoute('app_profile');
    }
}
