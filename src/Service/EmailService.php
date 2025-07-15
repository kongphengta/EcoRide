<?php

namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $mailerFrom,
        private string $mailerFromName
    ) {}

    /**
     * Envoie l'e-mail de vérification après l'inscription.
     */
    public function sendVerificationEmail(User $user, string $verificationUrl): bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, $this->mailerFromName))
            ->to($user->getEmail())
            ->subject('Confirmez votre adresse e-mail pour EcoRide')
            ->htmlTemplate('emails/registration_verification.html.twig')
            ->context([
                'user' => $user,
                'verificationUrl' => $verificationUrl,
            ]);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Erreur de transport lors de l\'envoi de l\'email de vérification: ' . $e->getMessage(), [
                'exception' => $e,
                'user_email' => $user->getEmail()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur générale lors de l\'envoi de l\'email de vérification: ' . $e->getMessage(), [
                'exception' => $e,
                'user_email' => $user->getEmail()
            ]);
        }

        return false;
    }

    /**
     * Envoie l'e-mail de réinitialisation de mot de passe.
     */
    public function sendPasswordResetEmail(User $user, string $resetToken, int $tokenLifetimeInMinutes): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->mailerFrom, $this->mailerFromName))
            ->to($user->getEmail())
            ->subject('Votre demande de réinitialisation de mot de passe')
            ->htmlTemplate('emails/reset_password.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetimeInMinutes' => $tokenLifetimeInMinutes,
            ]);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // On log l'erreur mais on ne la propage pas pour ne pas révéler si l'email existe ou non.
            // Le contrôleur affichera un message générique dans tous les cas.
            $this->logger->error('Erreur de transport lors de l\'envoi de l\'email de reset password: ' . $e->getMessage(), [
                'exception' => $e,
                'user_email' => $user->getEmail()
            ]);
        } catch (\Exception $e) {
            $this->logger->error('Erreur générale lors de l\'envoi de l\'email de reset password: ' . $e->getMessage(), [
                'exception' => $e,
                'user_email' => $user->getEmail()
            ]);
        }
    }
}
