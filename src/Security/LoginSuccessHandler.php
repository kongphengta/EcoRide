<?php
// src/Security/LoginSuccessHandler.php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): ?Response
    {
        /**
         * @var \App\Entity\User | null $user
         */
        $user = $token->getUser();

        if ($user instanceof User) {
            if (!$user->isProfileComplete()) {
                // Tenter d'ajouter le message flash
                $this->addFlashIfSessionIsFlashBagAware($request, 'info', 'Veuillez compléter votre profil pour continuer.');
                return new RedirectResponse($this->urlGenerator->generate('app_complete_profile'));
            }
            // Si le profil est complet, rediriger vers la page de profil
            return new RedirectResponse($this->urlGenerator->generate('app_profile'));
        }

        // Cas par défaut si $user n'est pas une instance de User (ne devrait pas arriver normalement)
        // ou si une autre logique l'exigeait. Rediriger vers la page d'accueil.
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    /**
     * Ajoute un message flash si la session de la requête implémente FlashBagAwareSessionInterface.
     */
    private function addFlashIfSessionIsFlashBagAware(Request $request, string $type, string $message): void
    {
        // Vérifier si la requête a une session
        if (!$request->hasSession()) {
            return;
        }

        $session = $request->getSession();

        // Vérifier si la session est du bon type pour gérer les flash bags
        if ($session instanceof FlashBagAwareSessionInterface) {
            // Maintenant, on est sûr que getFlashBag() existe et est utilisable
            $session->getFlashBag()->add($type, $message);
        }
        // Si la session n'est pas du bon type, on ne fait rien (pas de message flash)
    }
}
