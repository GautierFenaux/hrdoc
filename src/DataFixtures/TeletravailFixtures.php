<?php

namespace App\DataFixtures;

use DateTime;
use DateTimeImmutable;
use App\Entity\TeletravailForm;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TeletravailFixtures extends Fixture
{
    public function __construct(private UserRepository $userRepository) {}

    public function load(ObjectManager $manager): void
    {
        // Generate a random timestamp within the range
        $dates = ['2024'];
        $states = ['attente-manager', 'validé-manager', 'refus-rh','validé-rh', 'validé', 'refus-manager','refus-manager'];

        for ($i = 1; $i <= 61; $i++) {

            $collaborator = $this->userRepository->find($i);

            $randomYear = $dates[array_rand($dates, 1)];
            $randomDayMonth = '01-' . rand('01', '12');
            $aCompterDu = DateTime::createFromFormat('d-m-Y', $randomDayMonth . '-' . $randomYear);
            $dateFinteletravail = DateTime::createFromFormat('d-m-Y', '31-12-' . date('Y'));
            $dateFinteletravail->setTime(00, 00, 00);

                $teletravail = (new TeletravailForm())
                ->setUser($collaborator)
                ->setManager($collaborator->getManager())
                ->setNatureContrat('CDI')
                ->setLieuTeletravail('15 rue du test, Villetest 12345')
                ->setFonctionExercee('fonction générique')
                ->setAvisManager(true)
                ->setAvisDRH(true)
                // ->setState($states[array_rand($states)])
                ->setAttestationAssurance('/app/upload/attestations/9656b9e79a1e3f6b3813913b95701119.pdf')
                ->setAttestationHonneur(true)
                ->setConnexionInternet(true)
                ->setACompterDu($aCompterDu)
                ->setDateFinTeletravail($dateFinteletravail)
                ->setCreatedAt(new DateTimeImmutable());

            $manager->persist($teletravail);
        

        
        }
        $manager->flush();
    }
}
