<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'request_password_reset')]
    public function request(
        Request $request,
        UserRepository $userRepository,
        EmailService $emailService,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $user->setResetTokenCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($user);
                $entityManager->flush();

                // Utilisation du service d'email
                $emailService->sendPasswordResetEmail(
                    $user,
                    $token,
                    $this->getParameter('app.reset_password_token_lifetime') / 60
                );
            }

            $this->addFlash('success', 'Si un compte correspond à votre adresse e-mail, un lien pour réinitialiser votre mot de passe vous a été envoyé.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
            'breadcrumb' => [
                ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
                ['label' => 'Réinitialiser mot de passe', 'url' => $this->generateUrl('request_password_reset')],
            ],
        ]);
    }

    #[Route('/reset-password/{token}', name: 'reset_password_check')]
    public function reset(
        Request $request,
        string $token,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        LoggerInterface $logger
    ): Response {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Ce lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('request_password_reset');
        }

        // Vérifier si le token a expiré en utilisant les secondes de services.yaml
        $tokenLifetimeInSeconds = $this->getParameter('app.reset_password_token_lifetime');
        $tokenLifetime = new \DateInterval('PT' . $tokenLifetimeInSeconds . 'S');
        $tokenExpiresAt = (clone $user->getResetTokenCreatedAt())->add($tokenLifetime);

        if (new \DateTimeImmutable() > $tokenExpiresAt) {
            $logger->info('Tentative de réinitialisation avec un token expiré.', ['token' => $token]);
            $this->addFlash('danger', 'Ce lien de réinitialisation a expiré. Veuillez faire une nouvelle demande.');
            return $this->redirectToRoute('request_password_reset');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken(null);
            $user->setResetTokenCreatedAt(null);

            $user->setPassword(
                $passwordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
            'breadcrumb' => [
                ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
                ['label' => 'Nouveau mot de passe', 'url' => ''],
            ],
        ]);
    }
}
