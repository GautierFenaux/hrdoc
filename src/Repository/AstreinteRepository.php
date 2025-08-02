<?php

namespace App\Repository;

use App\Entity\Astreinte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Astreinte>
 *
 * @method Astreinte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Astreinte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Astreinte[]    findAll()
 * @method Astreinte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AstreinteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Astreinte::class);
    }

    /**
     * @return Astreinte[] 
     * Retourne un tableau avec les demandes d'astreinte en fonction des états passés en second paramètre
     */
    public function findByStatus(int $userId, array $values): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->where('a.user = :val')
            ->setParameter('val', $userId)
            ->andWhere('a.state IN (:values)')
            ->setParameter('values', $values);

        $query = $queryBuilder->getQuery();
        return $query->getResult(); 
    }

    //    /**
    //     * @return Astreinte[] Returns an array of Astreinte objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Astreinte
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
