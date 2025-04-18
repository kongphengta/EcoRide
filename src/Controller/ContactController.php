<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(): Response
    {
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Contact', 'url' => $this->generateUrl('app_contact')],
        ];
        return $this->render('contact/index.html.twig', [
            'breadcrumb' => $breadcrumb, // On passe le fil d'ariane au template
        ]);
    }
}
