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

        public function findByReleaseDate(): array
        {
            return $this->createQueryBuilder('g')
                ->orderBy('g.release_date', 'DESC')
                ->setMaxResults(6)
                ->getQuery()
                ->getResult()
            ;
        }

        public function findByPromotion()
        {
            return $this->createQueryBuilder('g')
                ->andWhere('g.promotion IS NOT NULL')
                ->getQuery()
                ->getResult();
        }


        public function findOneById($id): ?Game
        {
            return $this->createQueryBuilder('g')
                ->andWhere('g.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
}
