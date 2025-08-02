<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/profil/public')]
class PublicProfileController extends AbstractController
{
    #[Route('/{id}', name: 'app_public_profile_show', methods: ['GET'])]
    public function show(User $user, PaginatorInterface $paginator, Request $request, AvisRepository $avisRepository): Response
    {
        // On utilise le repository pour créer une requête DQL, ce qui est beaucoup plus performant
        // que de charger tous les avis en mémoire avec getAvisRecus()->matching().
        $avisRecusQuery = $avisRepository->createQueryBuilderForAvisRecus($user);

        $pagination = $paginator->paginate(
            $avisRecusQuery, // On passe le QueryBuilder, pas la collection
            $request->query->getInt('page', 1), // Le numéro de la page, 1 par défaut
            5 // Nombre d'avis par page
        );

        return $this->render('public_profile/show.html.twig', [
            'user' => $user,
            'avisRecus' => $pagination,
        ]);
    }
}
