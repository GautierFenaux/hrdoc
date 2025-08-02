<?php

namespace App\Repository;

use App\Entity\RetourSurSite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RetourSurSite>
 *
 * @method RetourSurSite|null find($id, $lockMode = null, $lockVersion = null)
 * @method RetourSurSite|null findOneBy(array $criteria, array $orderBy = null)
 * @method RetourSurSite[]    findAll()
 * @method RetourSurSite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RetourSurSiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RetourSurSite::class);
    }

    //    /**
    //     * @return RetourSurSite[] Returns an array of RetourSurSite objects
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

    //    public function findOneBySomeField($value): ?RetourSurSite
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    
    /**
     * @return RetourSurSite[] 
     * Retourne un tableau avec les formulaires en fonction des états passés en second paramètre
     */
    public function findByStatus(int $userId, array $values): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->where('r.user = :val')
            ->setParameter('val', $userId)
            ->andWhere('r.state IN (:values)')
            ->setParameter('values', $values);

        $query = $queryBuilder->getQuery();
        return $query->getResult(); 
    }

       /**
     * @return RetourSurSite[] 
     * Retourne un tableau avec les formulaires en fonction des états passés en second paramètre
     */
    public function findByStatusAndManager(int $managerId, array $values): array
    {
        $queryBuilder = $this->createQueryBuilder('r')
            ->where('r.manager = :val')
            ->setParameter('val', $managerId)
            ->andWhere('r.state IN (:values)')
            ->setParameter('values', $values);

        $query = $queryBuilder->getQuery();
        return $query->getResult(); 
    }
}
