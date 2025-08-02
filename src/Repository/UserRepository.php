<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findManagerByDepartement($value): array
    {
        $result = $this->createQueryBuilder('u')
            ->andWhere('u.departement LIKE :val')
            ->setParameter('val', '%' . $value . '%')
            ->andWhere("u.metier LIKE :val2")
            ->setParameter('val2', '%' . 'Responsable' . '%')
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findDepartements(): array
    {


        $result = $this->createQueryBuilder('u')
            ->select('DISTINCT u.departement')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function countWithoutTeletravailForYear(int $year): int
    {
        $start = new \DateTimeImmutable("$year-01-01");
        $end = new \DateTimeImmutable("$year-12-31");

        return (int) $this->createQueryBuilder('u')
            ->select('COUNT(u.id)') // ✅ Only count the users
            ->leftJoin('u.teletravailForms', 't', 'WITH', 't.aCompterDu BETWEEN :start AND :end')
            ->where('t.id IS NULL AND u.eligibleTT = true')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult(); // ✅ Works now, returns one number
    }
}
