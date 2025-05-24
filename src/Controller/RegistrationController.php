<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
//use Symfony\Component\Mime\Email; // Remplacé par TemplatedEmail
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Psr\Log\LoggerInterface;

class RegistrationController extends AbstractController
{
    private LoggerInterface $logger; // Ajoutez cette propriété

    // Modifiez le constructeur pour injecter le logger
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator,
        UrlGeneratorInterface $urlGenerator
    ): Response {
        if ($this->getUser()) {
            $this->addFlash('info', 'Vous êtes déjà connecté.');
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $token = $tokenGenerator->generateToken();
            $user->setVerificationToken($token);
            $user->setIsVerified(false);
            $user->setIsProfileComplete(false);

            // Hasher le mot de passe
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $verificationUrl = $urlGenerator->generate(
                'app_verify_email', // Nom de la route pour la vérification
                ['id' => $user->getId(), 'token' => $token],
                UrlGeneratorInterface::ABSOLUTE_URL // Important pour l'email
            );

            // Créer l'email
            $email = (new TemplatedEmail())
                ->from(new Address($this->getParameter('app.mailer_from'), $this->getParameter('app.mailer_from_name'))) // Configurez dans services.yaml ou .env
                ->to($user->getEmail())
                ->subject('Confirmez votre adresse e-mail pour EcoRide')
                ->htmlTemplate('emails/registration_verification.html.twig') // Créez ce template
                ->context([
                    'user' => $user,
                    'verificationUrl' => $verificationUrl,
                    // Optionnel: définir une durée de validité du token si nécessaire
                ]);

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Inscription réussie ! Un email de vérification vous a été envoyé. Veuillez consulter votre boîte de réception pour activer votre compte.');
            } catch (\Exception $e) {
                // Log l'erreur exacte !
                $this->logger->error('Erreur lors de l\'envoi de l\'email de vérification: ' . $e->getMessage(), [
                    'exception' => $e
                ]);
                // $logger->error('Erreur envoi email vérification: '.$e->getMessage());
                $this->addFlash('warning', 'Inscription réussie, mais l\'email de vérification n\'a pas pu être envoyé. Contactez l\'administrateur.');
                // On pourrait vouloir supprimer l'utilisateur ou permettre une re-génération du token plus tard
            }

            // --- MODIFIÉ : Ne PAS stocker l'ID en session ici ---
            // $request->getSession()->set('user_id_to_complete_profile', $user->getId()); // Supprimé

            // --- MODIFIÉ : Redirection vers la page de connexion (ou une page dédiée) ---
            return $this->redirectToRoute('app_login'); // Ou 'app_home' ou une route 'check_email_page'
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    // --- Action verifyUserEmail : à réactiver et implémenter ---
    #[Route('/verify/email/{id}/{token}', name: 'app_verify_email')] // Ajout des paramètres id et token
    public function verifyUserEmail(
        Request $request, // Peut être utile, sinon à supprimer
        EntityManagerInterface $entityManager,
        int $id, // Récupère l'ID depuis l'URL
        string $token // Récupère le token depuis l'URL
        // UserRepository $userRepository // Injecter si non déjà fait via $entityManager
    ): Response {
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['id' => $id, 'verification_token' => $token]);

        // Vérifier si l'utilisateur existe et si le token correspond
        if (null === $user) {
            $this->addFlash('danger', 'Lien de vérification invalide ou expiré.');
            return $this->redirectToRoute('app_register'); // Ou app_login
        }

        // Vérifier si le compte est déjà vérifié
        if ($user->isVerified()) {
            $this->addFlash('info', 'Votre compte est déjà vérifié. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        // Marquer comme vérifié et supprimer le token
        $user->setIsVerified(true);
        $user->setVerificationToken(null); // Important pour la sécurité et éviter réutilisation

        $entityManager->persist($user); // Pas strictement nécessaire si l'objet est déjà managé, mais ne nuit pas
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte a été vérifié avec succès ! Vous pouvez maintenant vous connecter.');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/inscription/succes', name: 'app_registration_success')]
    public function registrationSuccess(): Response
    {
        // Cette route n'est plus utilisée dans le flux modifié, mais peut être gardée ou supprimée
        return $this->render('registration/success.html.twig');
    }

    #[Route('/complete/profile', name: 'app_complete_profile')]
    // #[IsGranted('IS_AUTHENTICATED_FULLY')] // Ajouter une sécurité pour s'assurer que l'utilisateur est connecté

    public function completeProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // --- Récupérer l'utilisateur connecté ---
        /** @var User|null $user */
        $user = $this->getUser();

        // Si pas connecté, rediriger vers la connexion
        if (!$user) {
            $this->addFlash('warning', 'Veuillez vous connecter pour compléter votre profil.');
            return $this->redirectToRoute('app_login');
        }

        // --- MODIFIÉ : Plus besoin de récupérer depuis la session ---
        // $userId = $request->getSession()->get('user_id_to_complete_profile');
        // ... (logique de session supprimée) ...

        // Vérifier si le profil est déjà marqué comme complet
        if ($user->isProfileComplete()) {
            // $request->getSession()->remove('user_id_to_complete_profile'); // Plus nécessaire
            $this->addFlash('info', 'Votre profil est déjà complet.');
            return $this->redirectToRoute('app_profile'); // Rediriger vers le profil normal ou l'accueil
        }

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsProfileComplete(true);
            $entityManager->flush();

            // --- Session déjà nettoyée car non utilisée ---
            // $request->getSession()->remove('user_id_to_complete_profile');

            $this->addFlash('success', 'Profil complété avec succès ! Vous pouvez maintenant utiliser toutes les fonctionnalités.');

            // --- Redirection vers l'accueil ou le profil ---
            return $this->redirectToRoute('app_profile'); // Ou 'app_profile'
        }

        return $this->render('registration/complete_profile.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user // Passer l'utilisateur au template si nécessaire
        ]);
    }

    #[Route('/profile/success', name: 'app_profile_success')]
    public function profileSuccess(): Response
    {
        // Cette route n'est plus utilisée dans le flux modifié, mais peut être gardée ou supprimée
        return $this->render('registration/profile_success.html.twig');
    }
}
