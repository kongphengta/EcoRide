<?php
// src/Controller/CovoiturageController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturages', name: 'app_covoiturages')]
    public function index(): Response
    {
        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Covoiturages', 'url' => $this->generateUrl('app_covoiturages')],
        ];

        return $this->render('covoiturage/index.html.twig', [
            'breadcrumb' => $breadcrumb,
        ]);
    }
}
