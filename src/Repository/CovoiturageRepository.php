<?php
// f:\xampp\htdocs\ecoride\src\Repository\CovoiturageRepository.php
namespace App\Repository;

use App\Entity\Covoiturage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Covoiturage>
 *
 * @method Covoiturage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Covoiturage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Covoiturage[] findAll()
 * @method Covoiturage[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CovoiturageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Covoiturage::class);
    }

    /**
     * Recherche les covoiturages en fonction des critères.
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function searchCovoituragesQueryBuilder(array $criteria): \Doctrine\ORM\QueryBuilder
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.chauffeur', 'ch') // Pour accéder aux infos du chauffeur si besoin
            ->leftJoin('c.voiture', 'v') // Pour accéder aux infos de la voiture si besoin
            ->addSelect('ch', 'v'); // S'assurer que les entités jointes sont chargées

        if (!empty($criteria['depart'])) {
            $qb->andWhere('c.lieuDepart LIKE :lieuDepart')
                ->setParameter('lieuDepart', '%' . $criteria['depart'] . '%');
        }

        if (!empty($criteria['arrivee'])) {
            $qb->andWhere('c.lieuArrivee LIKE :lieuArrivee')
                ->setParameter('lieuArrivee', '%' . $criteria['arrivee'] . '%');
        }

        if (!empty($criteria['date'])) {
            /** @var \DateTime $date */
            $date = $criteria['date'];
            // Recherche pour la journée entière
            $dateDebut = (clone $date)->setTime(0, 0, 0);
            $dateFin = (clone $date)->setTime(23, 59, 59);
            $qb->andWhere('c.dateDepart BETWEEN :dateDebut AND :dateFin')
                ->setParameter('dateDebut', $dateDebut)
                ->setParameter('dateFin', $dateFin);
        }

        if (!empty($criteria['prixMax'])) {
            $qb->andWhere('c.prixPersonne <= :prixMax')
                ->setParameter('prixMax', $criteria['prixMax']);
        }

        if (!empty($criteria['ecologique']) && $criteria['ecologique'] === true) {
            $qb->andWhere('v.motorisation = :motorisation')
                ->setParameter('motorisation', 'Électrique');
        }

        // On ne motre que les covoiturages qui sont "proposés" et dont la date n'est pas passée.
        $qb->andWhere('c.statut NOT IN (:excluded_statuts)')
            ->setParameter('excluded_statuts', ['Annulé', 'Terminé', 'Passé']);

        $qb->andWhere('c.dateDepart >= :today') // S'assurer que la date de départ est aujourd'hui ou future.
            ->setParameter('today', (new \DateTimeImmutable('today'))->setTime(0, 0, 0));

        // US 3: On ne montre que les covoiturages avec au moins une place disponible.
        $qb->andWhere('c.nbPlaceRestantes > 0');

        if (!empty($criteria['noteMinimale'])) {
            $qb->leftJoin('ch.avisRecus', 'a')
                ->groupBy('c.id, ch.id, v.id') // Grouper pour pouvoir utiliser AVG()
                ->having('AVG(a.note) >= :noteMinimale')
                ->setParameter('noteMinimale', $criteria['noteMinimale']);
        }

        // Trier par date de départ la plus proche
        $qb->orderBy('c.dateDepart', 'ASC')
            ->addOrderBy('c.heureDepart', 'ASC');

        return $qb;
    }
    /**
     * @return Covoiturage[] Returns an array of upcoming Covoiturage objects
     */
    public function findUpcoming(string $order = 'ASC'): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.dateDepart >= :today')
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('c.dateDepart', $order)
            ->addOrderBy('c.heureDepart', $order)
            ->getQuery()
            ->getResult();
    }
}
