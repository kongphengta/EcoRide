<?php

namespace App\Security;


use App\Repository\UserRepository;
use App\Security\LoginSuccessHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;
    public const LOGIN_ROUTE = 'app_login';
    // private LoginSuccessHandler $loginSuccessHandler;


    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository,
        private LoginSuccessHandler $loginSuccessHandler
    ) {
        $this->loginSuccessHandler = $loginSuccessHandler;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username');
        if (null === $email) {
            throw new BadCredentialsException('Email (username) field must be set.');
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Vérifier s'il y a un chemin cible (redirection après accès refusé)
        // if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        //     return new RedirectResponse($targetPath);
        // }


        // Rediriger vers la page d'accueil par défaut
        // return new RedirectResponse($this->urlGenerator->generate('app_home'));
        return $this->loginSuccessHandler->onAuthenticationSuccess($request, $token);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
