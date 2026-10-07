<?php

namespace App\Repository;

use App\Entity\SalesOrder;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SalesOrder>
 */
class SalesOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SalesOrder::class);
    }

    /**
     * Retourne un tableau d'objets SalesOrder avec leurs produits associés pour l'utilisateur spécifié.
     *
     * @param User $user L'utilisateur pour lequel récupérer les commandes.
     *
     * @return SalesOrder[] Retourne un tableau d'objets SalesOrder avec leurs produits associés pour l'utilisateur spécifié.
     */
    public function findByUserWithProducts(User $user): array
    {
        return $this->createQueryBuilder('o')
            ->addSelect('p') // Alias pour salesOrderProducts
            ->leftJoin('o.salesOrderProducts', 'p')
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return SalesOrder[] Returns an array of SalesOrder objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?SalesOrder
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
