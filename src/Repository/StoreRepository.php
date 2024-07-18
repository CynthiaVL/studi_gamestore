<?php

namespace App\Repository;

use App\Entity\Store;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Store>
 */
class StoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Store::class);
    }

    public function findOneById($id)
    {
        return $this->createQueryBuilder('s')
                ->andWhere('s.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult()
            ;
    }

    public function findNearestStore($latitude, $longitude)
    {
        $entityManager = $this->getEntityManager();

        // Utilisez une requête SQL native pour calculer la distance
        $query = $entityManager->createQuery(
            'SELECT s, 
                    (6371 * acos(cos(radians(:latitude)) 
                    * cos(radians(a.latitude)) 
                    * cos(radians(a.longitude) - radians(:longitude)) 
                    + sin(radians(:latitude)) 
                    * sin(radians(a.latitude)))) AS distance 
             FROM App\Entity\Store s 
             JOIN s.Adress a
             ORDER BY distance ASC'
        )->setParameter('latitude', $latitude)
         ->setParameter('longitude', $longitude)
         ->setMaxResults(1);

        // Exécutez la requête et retournez le résultat
        $nearestStore = $query->getOneOrNullResult();

        if ($nearestStore instanceof Store) {
            return $nearestStore;
        } else {
            return null; // Aucun magasin trouvé à proximité
        }
    }
}
