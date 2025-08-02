<?php

namespace App\Repository;

use App\Entity\TeletravailForm;
use Doctrine\ORM\Query\Parameter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<TeletravailForm>
 *
 * @method TeletravailForm|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeletravailForm|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeletravailForm[]    findAll()
 * @method TeletravailForm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeletravailFormRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeletravailForm::class);
    }

    /**
     * @return TeletravailForm[] 
     * Retourne un tableau avec les formulaires qui restent à valider pour un manager
     */
    public function findByState(int $managerId, string $state): array
    {

        return $this->createQueryBuilder('t')
            ->andWhere('t.state = :sta')
            ->setParameter('sta', $state)
            ->andWhere('t.manager = :man')
            ->setParameter('man', $managerId)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TeletravailForm[] 
     * Retourne un tableau avec les formulaires en fonction des états passés en second paramètre
     */
    public function findByStatus(int $userId, array $values): array
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->where('t.user = :val')
            ->setParameter('val', $userId)
            ->andWhere('t.state IN (:values)')
            ->setParameter('values', $values);

        $query = $queryBuilder->getQuery();
        return $query->getResult();
    }

    /**
     * @return int
     */
    public function countByYearAndStates(int $year, array $states): int
    {
        $startDate = new \DateTimeImmutable("$year-01-01");
        $endDate = new \DateTimeImmutable("$year-12-31");

        return (int) $this->createQueryBuilder('t')
            ->select('COUNT(t.id)') // ✅ Only one column selected
            ->where('t.aCompterDu BETWEEN :start AND :end')
            ->andWhere('t.state IN (:states)')
            ->setParameters([
                'start' => $startDate,
                'end' => $endDate,
                'states' => $states,
            ])
            ->getQuery()
            ->getSingleScalarResult(); // ✅ Returns a single value (int)
    }
}
