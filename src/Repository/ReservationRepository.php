<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Récupère les réservations d'un utilisateur, triées par date de départ du covoiturage.
     * @param \App\Entity\User $user
     * @return Reservation[]
     */
    public function findUserReservationsSortedByDate(\App\Entity\User $user): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.covoiturage', 'c') // 'c' est l'alias pour Covoiturage
            ->addSelect('c') // Important : pour hydrater l'objet Covoiturage et éviter des requêtes supplémentaires
            ->innerJoin('c.chauffeur', 'ch') // 'ch' est l'alias pour le chauffeur
            ->addSelect('ch') // Important : pour hydrater l'objet User du chauffeur
            ->where('r.passager = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateDepart', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les réservations annulées d'un utilisateur
     * @param \App\Entity\User $user
     * @return Reservation[]
     */
    public function findUserCanceledReservations(\App\Entity\User $user): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.passager = :user')
            ->andWhere('r.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', 'Annulé')
            ->orderBy('r.dateReservation', 'DESC')
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
