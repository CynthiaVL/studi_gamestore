<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\Store;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findAllOrderDelivred(?Store $store=null)
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', 'delivred');

        if ($store) {
            $queryBuilder->andWhere('o.store = :store')
                ->setParameter('store', $store);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function findByGameTitle(string $title)
    {
        return $this->createQueryBuilder('o')
            ->join('o.game', 'g')
            ->where('g.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getResult();
    }

    public function findValidatedOrdersByStore(Store $store)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->andWhere('o.store = :store')
            ->setParameter('status', 'validated')
            ->setParameter('store', $store)
            ->getQuery()
            ->getResult();
    }

    public function calculatePickupDate(){
        $pickupDate = new \DateTime();
        $daysToAdd = 0;

        while ($daysToAdd < 7) {
            $pickupDate->modify('+1 day');
            $dayOfWeek = $pickupDate->format('N');

            if ($dayOfWeek != 7 && $dayOfWeek != 1) { // 7 is Sunday, 1 is Monday
                $daysToAdd++;
            }
        }

        return $pickupDate;
    }
}
