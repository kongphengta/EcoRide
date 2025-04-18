<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CovoiturageResultatsController extends AbstractController
{
    #[Route('/covoiturage/resultats', name: 'app_covoiturages_resultats')]
    public function index(Request $request): Response
    {
        $depart = $request->query->get('depart');
        $arrivee = $request->query->get('arrivee');
        $date = $request->query->get('date');

        $breadcrumb = [
            ['label' => 'Accueil', 'url' => $this->generateUrl('app_home')],
            ['label' => 'Covoiturages', 'url' => $this->generateUrl('app_covoiturages')],
            ['label' => 'Résultats', 'url' => $this->generateUrl('app_covoiturages_resultats', [
                'depart' => $depart,
                'arrivee' => $arrivee,
                'date' => $date,
            ])],
        ];


        // Ici, je vais récupérer les covoiturages en fonction des critères
        // Pour l'instant, je vais juste afficher les critères de recherche

        return $this->render('covoiturage_resultats/index.html.twig', [
            'depart' => $depart,
            'arrivee' => $arrivee,
            'date' => $date,
            'breadcrumb' => $breadcrumb, // On passe le fil d'ariane au template
        ]);
    }
}
