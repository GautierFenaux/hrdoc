<?php

namespace App\Service;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Repository\TeletravailFormRepository;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExportService
{


    public function __construct(private TeletravailFormRepository $teletravailFormRepository, private ParameterBagInterface $parameterBag, private UserRepository $userRepository) {}


    public function exportData(string $type)
    {

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if (str_contains($type, 'cet')) {

            $users = $this->userRepository->findBy(['eligibleCet' => true]);

            $sheet->setTitle('Demande d\'alimentation');
            $sheet->setCellValue('A1', 'Nom');
            $sheet->setCellValue('B1', 'Prénom');
            $sheet->setCellValue('C1', 'Congé cumulé en jours');
            $sheet->setCellValue('D1', 'Jour de congés utilisés');
            $sheet->setCellValue('E1', 'Solde de jours de congés non pris');
            $sheet->setCellValue('F1', 'Nombre de jours versés');
            $sheet->setCellValue('G1', 'Etat de la demande');
            $sheet->getColumnDimension('A')->setWidth(30);
            $sheet->getColumnDimension('B')->setWidth(30);
            $sheet->getColumnDimension('C')->setWidth(30);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheet->getColumnDimension('G')->setWidth(30);


            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('Demande d\'utilisation');
            $sheet2->setCellValue('A1', 'Nom');
            $sheet2->setCellValue('B1', 'Prénom');
            $sheet2->setCellValue('C1', 'Nb de jours');
            $sheet2->setCellValue('D1', 'Nb de jours à débiter');
            $sheet2->setCellValue('E1', 'Date de début');
            $sheet2->setCellValue('F1', 'Date de fin');
            $sheet2->getColumnDimension('A')->setWidth(30);
            $sheet2->getColumnDimension('B')->setWidth(30);
            $sheet2->getColumnDimension('C')->setWidth(30);
            $sheet2->getColumnDimension('D')->setWidth(30);
            $sheet2->getColumnDimension('E')->setWidth(30);
            $sheet2->getColumnDimension('F')->setWidth(30);

            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('Demande de restitution');
            $sheet3->setCellValue('A1', 'Nom');
            $sheet3->setCellValue('B1', 'Prénom');
            $sheet3->setCellValue('C1', 'Nb jours liquidés');
            $sheet3->getColumnDimension('A')->setWidth(30);
            $sheet3->getColumnDimension('B')->setWidth(30);
            $sheet3->getColumnDimension('C')->setWidth(30);

            $rowAlim = 2;
            $rowUtil = 2;
            $rowResti = 2;

            foreach ($users as $user) {
                $cets = $user->getCet();
                foreach ($cets as $cet) {

                    if ($cet->isAlimentation() == true) {

                        $sheet->setCellValue('A' . $rowAlim, $user->getName());
                        $sheet->setCellValue('B' . $rowAlim, $user->getSurname());
                        $sheet->setCellValue('C' . $rowAlim, $cet->getDroitCongesCumule());
                        $sheet->setCellValue('D' . $rowAlim, $cet->getNbJoursCongesUtilises());
                        $sheet->setCellValue('E' . $rowAlim, $cet->getSoldeJoursCongesNonPris());
                        $sheet->setCellValue('F' . $rowAlim, $cet->getNbJoursVersement());
                        $sheet->setCellValue('G' . $rowAlim, $cet->getState()->value);
                        $rowAlim++;
                    } else if ($cet->isUtilisation() == true) {

                        $sheet2->setCellValue('A' . $rowUtil, $user->getName());
                        $sheet2->setCellValue('B' . $rowUtil, $user->getSurname());
                        $sheet2->setCellValue('C' . $rowUtil, $cet->getNbJours());
                        $sheet2->setCellValue('D' . $rowUtil, $cet->getNbJoursADebiter());
                        $sheet2->setCellValue('E' . $rowUtil, $cet->getPriseCetDebut() ? $cet->getPriseCetDebut()->format('d-m-Y') : 'N/A');
                        $sheet2->setCellValue('F' . $rowUtil, $cet->getPriseCetFin() ? $cet->getPriseCetFin()->format('d-m-Y') : 'N/A');
                        $rowUtil++;
                    } else {

                        $sheet3->setCellValue('A' . $rowResti, $user->getName());
                        $sheet3->setCellValue('B' . $rowResti, $user->getSurname());
                        $sheet3->setCellValue('C' . $rowResti, $cet->getNbJoursLiquide());
                        $rowResti++;
                    }
                }
            }

            $filePath = $this->parameterBag->get('kernel.project_dir') . '/upload/export/' . 'export_cet_' . date('d-m-Y') . '.xlsx';
            $fileName = 'export_cet_' . date('d-m-Y') . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            return [$filePath, $fileName];
        }

        if (str_contains($type, 'teletravail-reminder')) {
            $collaborators = $this->userRepository->findBy(['eligibleTT' => true]);
            $emptyTTForCurrentYear = [];
            $existingTTForCurrentYear = [];
            foreach ($collaborators as $collaborator) {
                $teletravails = $collaborator->getTeletravailForms();
                $collabInfos = $collaborator->getId() . '-' . str_replace('-', ' ', $collaborator->getName()) . ' ' . $collaborator->getSurname();
                if (!$teletravails->isEmpty()) {
                    foreach ($teletravails as $teletravail) {
                        // Check si tt est pour l'année en cours
                        if ($teletravail->getACompterDu()->format('Y') == date('Y')) {
                            // false et ne sera pas affiché dans la vue des relances, sauf si état, obligé de faire comme ça car twig n'arrive pas à checker si string dans array...
                            // Si état ok pour relance alors on push l'état pour l'afficher dans la vue, sinon statut not ok pr relance.
                            if ($teletravail->getState()->value === 'validé-rh' || $teletravail->getState()->value === 'attente-manager') {
                                $existingTTForCurrentYear[$collabInfos][] = $teletravail->getState()->value;
                            } else {
                                $existingTTForCurrentYear[$collabInfos][] = true;
                            }
                        } else {
                            // true si formulaire n'est pas pour l'année en cours, collab ne sera pas affiché dans la vue des relances
                            $emptyTTForCurrentYear[$collabInfos][] = true;
                        }
                    }
                } else {
                    // true si aucun formulaire
                    $emptyTTForCurrentYear[$collabInfos][] = true;
                }
                // Supprime les tt qui ne doivent pas être relancés
                if (array_key_exists($collabInfos, $existingTTForCurrentYear)) {
                    foreach ($existingTTForCurrentYear[$collabInfos] as $existingTT) {
                        // On cherche si il y a d'autres formulaires existants via true, on les supprime si oui, pour ne garder que les formulaires à relancer.
                        if (($key = array_search('true', $existingTTForCurrentYear[$collabInfos])) !== false) {
                            unset($existingTTForCurrentYear[$collabInfos][$key]);
                            $existingTTForCurrentYear[$collabInfos] = array_values($existingTTForCurrentYear[$collabInfos]);
                        }
                        unset($emptyTTForCurrentYear[$collabInfos]);
                    }
                }
            }
            $sheet->setTitle('Demande non-complete');
            $sheet->setCellValue('A1', 'Nom');
            $sheet->setCellValue('B1', 'Prénom');
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(20);
            // Populate data
            $row = 2;
            foreach ($emptyTTForCurrentYear as $key => $value) {
                $key = explode(' ', $key);
                if(count($key) > 2) {
                    $sheet->setCellValue('A' . $row,  ltrim(strstr($key[0], '-'), '-').'-'.$key[1]);
                    $sheet->setCellValue('B' . $row,  $key[2]);
                } else {
                    $sheet->setCellValue('A' . $row,  ltrim(strstr($key[0], '-'), '-'));
                    $sheet->setCellValue('B' . $row,  $key[1]);
                }
                $row++;
            }

            $filePath = $this->parameterBag->get('kernel.project_dir') . '/upload/export/' . 'export_demande_tt_non_completees_' . date('d-m-Y') . '.xlsx';
            $fileName = 'export_demande_tt_non_completees' . date('d-m-Y') . '.xlsx';
            $writer = new Xlsx($spreadsheet);
            $writer->save($filePath);
            return [$filePath, $fileName];

        }


        $teletravailForms = $this->teletravailFormRepository->findAll();

        // Add header row
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Prénom');
        $sheet->setCellValue('C1', 'Nom');
        $sheet->setCellValue('D1', 'Date de la demande');
        $sheet->setCellValue('E1', 'État de la demande');
        $sheet->setCellValue('F1', 'Avis manager');
        $sheet->setCellValue('G1', 'Commentaire manager');
        $sheet->setCellValue('H1', 'Avis RH');
        $sheet->setCellValue('I1', 'Commentaire RH');
        $sheet->setCellValue('J1', 'Journées télétravaillées');
        $sheet->setCellValue('K1', 'activité éligible');


        $sheet->setAutoFilter($sheet->calculateWorksheetDimension());

        $sheet->getColumnDimension('A')->setWidth(7);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(25);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(25);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(25);
        $sheet->getColumnDimension('J')->setWidth(25);
        $sheet->getColumnDimension('K')->setWidth(25);


        // Populate data
        $row = 2;
        foreach ($teletravailForms as $teletravailForm) {

            $sheet->setCellValue('A' . $row, $teletravailForm->getId());
            $sheet->setCellValue('B' . $row, $teletravailForm->getUser()->getName());
            $sheet->setCellValue('C' . $row, $teletravailForm->getUser()->getSurname());
            $sheet->setCellValue('D' . $row, $teletravailForm->getCreatedAt()->format('d/m/Y'));
            $sheet->setCellValue('E' . $row, $teletravailForm->getState()->value);
            $sheet->setCellValue('F' . $row, $teletravailForm->isAvisManager() ? 'favorable' : 'défavorable');
            $sheet->setCellValue('G' . $row, $teletravailForm->getCommentaireManager());
            $sheet->setCellValue('H' . $row, $teletravailForm->isAvisDRH() ? 'favorable' : 'défavorable');
            $sheet->setCellValue('I' . $row, $teletravailForm->getCommentaireDRH());
            $sheet->setCellValue('J' . $row, implode(',', $teletravailForm->getJourneesTeletravaillees()));
            $sheet->setCellValue('K' . $row, $teletravailForm->isActiviteEligible());

            $row++;
        }

        // Save the spreadsheet to a file
        $filePath = $this->parameterBag->get('kernel.project_dir') . '/upload/export/' . 'export_teletravail_' . date('d-m-Y') . '.xlsx';
        $fileName = 'export_teletravail_' . date('d-m-Y') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);
        return [$filePath, $fileName];
    }
}
