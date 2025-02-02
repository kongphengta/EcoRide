<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\TrajetRepository;

class ApiController extends AbstractController
{
    #[Route('/api/search', name: 'api_search', methods: ['GET'])]
    public function search(Request $request, TrajetRepository $trajetRepository): Response
    {
        $lieuDepart = $request->query->get('lieu_depart');
        $lieuArrivee = $request->query->get('lieu_arrivee');

        $trajets = $trajetRepository->findByLieuDepartAndLieuArrivee($lieuDepart, $lieuArrivee);

        return $this->json(['trajets' => $trajets]);
    }
}


