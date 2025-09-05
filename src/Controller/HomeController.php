<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CovoiturageRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Form\CovoiturageSearchType;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(CovoiturageRepository $covoiturageRepository): Response
    {
        // On récupère les 2 prochains covoiturages à venir (ordre décroissant)
        $covoiturages = $covoiturageRepository->createQueryBuilder('c')
            ->leftJoin('c.chauffeur', 'ch')->addSelect('ch')
            ->leftJoin('c.voiture', 'v')->addSelect('v')
            ->where('c.statut NOT IN (:excluded_statuts)')
            ->setParameter('excluded_statuts', ['Annulé', 'Terminé', 'Passé'])
            ->andWhere('c.dateDepart >= :today')
            ->setParameter('today', (new \DateTimeImmutable('today'))->setTime(0, 0, 0))
            ->andWhere('c.nbPlaceRestantes > 0')
            ->orderBy('c.dateDepart', 'ASC')
            ->addOrderBy('c.heureDepart', 'ASC')
            ->setMaxResults(2)
            ->getQuery()
            ->getResult();

        return $this->render('home/index.html.twig', [
            'covoiturages' => $covoiturages,
        ]);
    }
}
