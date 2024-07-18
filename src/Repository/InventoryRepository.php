<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Inventory;
use App\Entity\Store;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Inventory>
 */
class InventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventory::class);
    }

    public function findQuantityByGameAndStore(Game $game, Store $store)
    {
        $result = $this->createQueryBuilder('i')
            ->select('i.quantity')
            ->where('i.game = :game')
            ->andWhere('i.store = :store')
            ->setParameter('game', $game)
            ->setParameter('store', $store)
            ->getQuery()
            ->getOneOrNullResult();

        return $result ? (int) $result['quantity'] : 0;
    }
}
