<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

        /**
         * @return Game[] Returns an array of Game objects
         */
        public function findByReleaseDate(): array
        {
            return $this->createQueryBuilder('g')
                ->orderBy('g.release_date', 'DESC')
                ->setMaxResults(6)
                ->getQuery()
                ->getResult()
            ;
        }

    /**
     * @return Game[] Returns an array of Game objects with promotions
     */
        public function findByPromotion()
        {
            return $this->createQueryBuilder('g')
                ->andWhere('g.promotion IS NOT NULL') // Adjust as per your entity's field name for promotion
                ->getQuery()
                ->getResult();
        }


        public function findOneBySomeField($value): ?Game
        {
            return $this->createQueryBuilder('g')
                ->andWhere('g.exampleField = :val')
                ->setParameter('val', $value)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
