<?php

namespace App\Repository;

use App\Entity\Cet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Cet>
 *
 * @method Cet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cet[]    findAll()
 * @method Cet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cet::class);
    }

    /**
     * @return Cet[] Returns an array of Cet objects
     */
    public function findByStatus(int $userId, array $values): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.user = :val')
            ->setParameter('val', $userId)
            ->andWhere('c.state IN (:values)')
            ->setParameter('values', $values);

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }
    /**
     * @return Cet[] Returns an array of Cet objects with createdAt dates between the first and last days of the current year
     */
    public function findByDateAndType(string $type)
    {
        $year = date('Y');

        $startDate = new \DateTimeImmutable("$year-01-01T00:00:00");
        $endDate = new \DateTimeImmutable("$year-12-31T23:59:59");

        $queryBuilder = $this->createQueryBuilder('c')
        ->where('c.createdAt BETWEEN :start AND :end');
        $exp = $queryBuilder->expr()->eq("c.$type", ':val');
        $queryBuilder->setParameter('val', 1)
            ->andWhere($exp)
            ->setParameter('start', $startDate)
            ->setParameter('end', $endDate);

        return $queryBuilder->getQuery()->getResult();
    }
}
