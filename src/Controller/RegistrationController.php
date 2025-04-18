<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_registration')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $userPasswordHasherInterface, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $user->setDateInscription(new \DateTimeImmutable());
        $form = $this->createForm(RegistrationFormType::class, $user); // Associer le formulaire à l'entité User
        $form->handleRequest($request);
        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $hashedPassword = $userPasswordHasherInterface->hashPassword(
                $user,
                $form->get('password')->getData()
            );
            $user->setPassword($hashedPassword);
            // Récupérer les données du formulaire
            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();
            // Authentifier l'utilisateur
            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            // Redirect to a success page or send a confirmation email
            return $this->redirectToRoute('app_registration_success');
        }
        return $this->render('registration/index.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }
    #[Route('/inscription/succes', name: 'app_registration_success')]
    public function registrationSuccess(): Response
    {
        return $this->render('registration/success.html.twig');
    }

    #[Route('/complete/profile', name: 'app_complete_profile')]
    public function completeProfile(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_registration');
        }

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les données du profil
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_profile_success');
        }

        return $this->render('registration/complete_profile.html.twig', [
            'profileForm' => $form->createView(),
        ]);
    }
    #[Route('/profile/success', name: 'app_profile_success')]
    public function profileSuccess(): Response
    {
        return $this->render('registration/profile_success.html.twig');
    }
}
