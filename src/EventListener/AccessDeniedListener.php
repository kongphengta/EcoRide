<?php
namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class AccessDeniedListener
{
    private $twig;
    private $authChecker;

    public function __construct(Environment $twig, AuthorizationCheckerInterface $authChecker)
    {
        $this->twig = $twig;
        $this->authChecker = $authChecker;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof AccessDeniedHttpException) {
            // Détection du rôle requis selon le contexte (exemple simple)
            $request = $event->getRequest();
            $route = $request->attributes->get('_route');
            $requiredRole = null;
            if (str_starts_with($route, 'admin')) {
                $requiredRole = 'ROLE_ADMIN';
            } elseif (str_starts_with($route, 'voiture')) {
                $requiredRole = 'ROLE_CHAUFFEUR';
            }
            $content = $this->twig->render('bundles/TwigBundle/Exception/error403.html.twig', [
                'required_role' => $requiredRole
            ]);
            $response = new Response($content, 403);
            $event->setResponse($response);
        }
    }
}
