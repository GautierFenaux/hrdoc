<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Cet;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CetFixtures extends Fixture
{
    public function __construct(private UserRepository $userRepository) {}

    public function load(ObjectManager $manager): void
    {



        $states = ['attente-manager', 'attente-rh', 'refus-rh', 'validé-rh', 'refus-manager'];

        for ($i = 1; $i <= 20; $i++) {

            $collaborator = $this->userRepository->find($i);

            $from = DateTime::createFromFormat('d-m-Y', '04-11-2024');
            $to = DateTime::createFromFormat('d-m-Y', '10-12-2024');

            $cetAlim = (new Cet())
                ->setAlimentation(true)
                ->setUser($collaborator)
                ->setManager($collaborator->getManager())
                ->setDroitCongesCumule(15)
                ->setNbJoursCongesUtilises(5)
                ->setSoldeJoursCongesNonPris(20)
                ->setNbJoursVersement(5)
                ->setAvisSupHierarchique(true)
                ->setAvisDRH(true)
                // ->setState(array_rand($states))
                // ->setState($states[array_rand($states, 1)])
                ->setCreatedAt(new DateTimeImmutable());

            $manager->persist($cetAlim);

            $cetUtil = (new Cet())
                ->setUser($collaborator)
                ->setManager($collaborator->getManager())
                ->setUtilisation(true)
                ->setNbJours(5)
                ->setSolde(1000)
                ->setNbJoursADebiter(10)
                ->setPriseCetDebut($from)
                ->setPriseCetFin($to)
                ->setAvisSupHierarchique(true)
                ->setAvisDRH(true)
                // ->setState($states[array_rand($states, 1)])
                ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($cetUtil);


            $cetResti = (new Cet())
                ->setUser($collaborator)
                ->setManager($collaborator->getManager())
                ->setRestitution(true)
                ->setNbJoursLiquide(6)
                ->setAvisSupHierarchique(true)
                ->setAvisDRH(true)
                // ->setState($states[array_rand($states, 1)])
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($cetResti);
        }
        $manager->flush();
    }
}
