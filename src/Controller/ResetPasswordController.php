<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use Psr\Log\LoggerInterface;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/forgot-password', name: 'request_password_reset')]
    public function request(
        Request $request,
        UserRepository $userRepository,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            // Si un utilisateur avec cet e-mail existe
            if ($user) {
                // dd($user);
                // Générer un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $user->setResetTokenCreatedAt(new \DateTimeImmutable());
                $entityManager->persist($user);
                $entityManager->flush();

                // Envoyer l'e-mail de réinitialisation
                try {
                    $logger->debug('Tentative d\'envoi de l\'e-mail', ['channel' => 'mailer']);

                    $email = (new TemplatedEmail())
                        ->from($this->getParameter('app.mailer_from')) // Configurer ceci dans config/services.yaml
                        ->to($user->getEmail())
                        ->subject('Votre demande de réinitialisation de mot de passe')
                        ->htmlTemplate('emails/reset_password.html.twig') // Je dois créer ce template ensuite
                        ->context([
                            'resetToken' => $token,
                            'tokenLifetime' => $this->getParameter('app.reset_password_token_lifetime'), // Configurer ceci
                        ]);

                    $mailer->send($email);

                    $this->addFlash('success', 'Un e-mail vous a été envoyé avec un lien pour réinitialiser votre mot de passe.');
                    return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion avec un message

                } catch (\Exception $e) {
                    $logger->error('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage(), ['channel' => 'mailer']);
                    $this->addFlash('reset_password_error', 'Une erreur est survenue lors de l\'envoi de l\'e-mail. Veuillez réessayer.');
                    return $this->redirectToRoute('request_password_reset');
                }
            }

            // // Si aucun utilisateur n'est trouvé, Je ne dois pas le révéler pour des raisons de sécurité
            // $this->addFlash('info', 'Si un compte correspond à cet e-mail, un lien de réinitialisation vous sera envoyé.');
            // return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    // // Route pour tester l'envoi d'e-mail
    // #[Route('/test-email', name: 'test_email')]
    // public function testEmail(MailerInterface $mailer): Response
    // {
    //     $email = (new TemplatedEmail())
    //         ->from($this->getParameter('app.mailer_from'))
    //         ->to('test@example.com') // Tu peux mettre une adresse fictive ici
    //         ->subject('Test Email from EcoRide')
    //         ->html('<p>Ceci est un e-mail de test.</p>');

    //     $mailer->send($email);

    //     dd('$email');
    // }
}
