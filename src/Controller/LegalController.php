<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LegalController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_legal_mentions')]
    public function mentionsLegales(): Response
    {
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Mentions légales', 'url' => $this->generateUrl('app_legal_mentions')],
        ];

        return $this->render('legal/mentions_legales.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/politique-confidentialite', name: 'app_legal_privacy')]
    public function politiqueConfidentialite(): Response
    {
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Politique de confidentialité', 'url' => $this->generateUrl('app_legal_privacy')],
        ];

        return $this->render('legal/politique_confidentialite.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }

    #[Route('/conditions-utilisation', name: 'app_legal_terms')]
    public function conditionsUtilisation(): Response
    {
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Conditions d\'utilisation', 'url' => $this->generateUrl('app_legal_terms')],
        ];

        return $this->render('legal/conditions_utilisation.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }
}
