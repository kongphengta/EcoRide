<?php

namespace App\Repository;

use App\Entity\Avis;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * Crée un QueryBuilder pour les avis reçus par un utilisateur,
     * triés par date de création décroissante.
     *
     * @param User $user Le receveur des avis.
     * @return QueryBuilder
     */
    public function createQueryBuilderForAvisRecus(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.receveur = :user')
            ->setParameter('user', $user)
            ->orderBy('a.dateCreation', 'DESC');
    }
}
