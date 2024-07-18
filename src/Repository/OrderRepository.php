<?php

namespace App\Repository;

use App\Entity\Order;
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
