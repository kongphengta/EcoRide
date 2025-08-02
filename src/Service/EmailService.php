<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

/**
 * Service centralisé pour l'envoi de tous les e-mails transactionnels de l'application.
 * Chaque méthode publique correspond à un type d'e-mail spécifique.
 */
class EmailService
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;
    private Address $sender;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger, string $senderEmail, string $senderName)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->sender = new Address($senderEmail, $senderName);
    }

    /**
     * Envoie un e-mail de vérification de compte après l'inscription.
     */
    public function sendVerificationEmail(User $user, string $verificationUrl): bool
    {
        return $this->send(
            $user->getEmail(),
            'Activez votre compte EcoRide',
            'emails/verification.html.twig',
            [
                'user' => $user,
                'verificationUrl' => $verificationUrl,
            ]
        );
    }

    /**
     * Envoie un e-mail contenant le lien de réinitialisation de mot de passe.
     */
    public function sendPasswordResetEmail(User $user, string $resetToken, int $tokenLifetime): bool
    {
        return $this->send(
            $user->getEmail(),
            'Réinitialisation de votre mot de passe EcoRide',
            'emails/reset_password.html.twig',
            [
                'user' => $user,
                'resetToken' => $resetToken,
                'tokenLifetime' => $tokenLifetime,
            ]
        );
    }

    /**
     * Notifie un passager que le chauffeur a annulé tout le covoiturage.
     */
    public function sendCovoiturageCancelledEmail(Reservation $reservation): bool
    {
        return $this->send(
            $reservation->getPassager()->getEmail(),
            'Annulation d\'un de vos trajets sur EcoRide',
            'emails/covoiturage_cancelled.html.twig',
            ['reservation' => $reservation]
        );
    }

    /**
     * Notifie un passager que le chauffeur a annulé sa réservation spécifique.
     */
    public function sendReservationCancelledByDriverEmail(Reservation $reservation): bool
    {
        return $this->send(
            $reservation->getPassager()->getEmail(),
            'Annulation de votre réservation sur EcoRide',
            'emails/reservation_cancelled_by_driver.html.twig',
            ['reservation' => $reservation]
        );
    }

    /**
     * Méthode privée pour construire et envoyer les e-mails.
     * Centralise la logique d'envoi et la gestion des erreurs.
     *
     * @return bool True si l'e-mail a été envoyé avec succès, false sinon.
     */
    private function send(string $to, string $subject, string $template, array $context): bool
    {
        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to($to)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        try {
            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Erreur lors de l\'envoi de l\'email', [
                'error' => $e->getMessage(),
                'to' => $to,
                'subject' => $subject,
            ]);
            return false;
        }
    }
    public function sendLeaveReviewEmail(Reservation $reservation, string $reviewUrl): bool
    {
        return $this->send(
            $reservation->getPassager()->getEmail(),
            '🌟 Votre avis compte ! - Trajet terminé avec ' . $reservation->getCovoiturage()->getChauffeur()->getPseudo(),
            'emails/leave_review.html.twig',  // ← Nom du template
            [
                'passager' => $reservation->getPassager(),
                'chauffeur' => $reservation->getCovoiturage()->getChauffeur(),
                'covoiturage' => $reservation->getCovoiturage(),
                'reviewUrl' => $reviewUrl
            ]
        );
    }
}
