<?php

namespace App\Controller;

use App\Entity\Astreinte;
use App\Form\AstreinteType;
use App\Service\SendMailService;
use App\Service\UrlGeneratorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/astreinte')]
class AstreinteController extends AbstractController
{

    public function __construct(
        private SendMailService $sendMailService,
        private UrlGeneratorService $urlGeneratorService,
    ) {}

    #[Route('/{id}', name: 'app_astreinte_show', methods: ['GET'])]
    public function show(Astreinte $astreinte): Response
    {
        return $this->render('collaborator/astreinte/show.html.twig', [
            'astreinte' => $astreinte,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_astreinte_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Astreinte $astreinte, EntityManagerInterface $entityManager): Response
    {
        $interval = false;
        $message = 'La demande d\'opération a bien été validée.';
        $nightTime = [];
        $dayTime = [];
        // On vérifie si l'astreinte a été validée par les RH afin d'afficher le bon formulaire dans le template
        if ($astreinte->isOkRh()) {
            $edit = 'isEditAfterRhValidation';
            $interval = $astreinte->getDebutAstreinte()->diff($astreinte->getFinAstreinte())->format('%a');
        } else {
            $edit = 'isEdit';
        }

        $form = $this->createForm(AstreinteType::class, $astreinte, [$edit => true, 'astreinte_days' => $interval]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($edit === 'isEditAfterRhValidation' || $astreinte->getState() == 'réouvert-rh-postop') {

                function getQuotientByDate($date)
                {
                    $dayOftheWeek = date('N', strtotime($date));
                    if ($dayOftheWeek == '6') {
                        return 1.25;
                    } else if ($dayOftheWeek == '7') {
                        return 1.5;
                    } else {
                        return 1;
                    }
                }

                // Permet de créer une date précise avec le jour bon jour (voir dans la boucle)
                $intervalArray = range(0, $interval);
                array_unshift($intervalArray, "");
                unset($intervalArray[0]);

                // $valuedTime = 0;
                $operationTimeArray = [];
                $lunchTimeArray = [];
                $timeSlot = [];
                for ($i = 1; $i <= $interval + 1; $i++) {
                    $day = $astreinte->getDebutAstreinte()->modify('+' . $intervalArray[$i] . 'days')->format('Y-m-d');
                    $startTime = new DateTime(
                        $day . $form->get('timeSlot_' . $i . '_1')->getData()
                    );
                    $endTime = new DateTime($day . $form->get('timeSlot_' . $i . '_4')->getData());

                    $startTimeLunch = new DateTime($day . $form->get('timeSlot_' . $i . '_2')->getData());
                    $endTimeLunch = new DateTime($day . $form->get('timeSlot_' . $i . '_3')->getData());

                    // $valuedQuotient = getQuotientByDate($startTime->format('Y-m-d'));
                    // Calcule du temps de l'opération et du déjeuner séparement
                    $operationTime = $startTime->diff($endTime);
                    $lunchBreak = $startTimeLunch->diff($endTimeLunch);
                    $totalTimeHours = $operationTime->h - $lunchBreak->h;
                    $totalTimeMinutes = ($operationTime->i - $lunchBreak->i) / 60;
                    // $totalTime = $totalTimeHours + $totalTimeMinutes;

                    // Convertit le total en minutes
                    $lunchMinutes = $lunchBreak->h * 60 + $lunchBreak->i;
                    $operationMinutes = $operationTime->h * 60 + $operationTime->i;

                    // soustrait le temps déjeuner de la journée de travail
                    $workedMinutes = $operationMinutes - $lunchMinutes;

                    // remets au bon format
                    $workedHours = intdiv($workedMinutes, 60);
                    $workedRemainingMinutes = $workedMinutes % 60;

                    $operationTimeArray[$startTime->format('Y-m-d')] = "{$workedHours}h{$workedRemainingMinutes}min";

                    $lunchTimeArray[$startTime->format('Y-m-d')] =
                        strval($lunchBreak->h) . 'h' .
                        ($lunchBreak->i < 10 ?
                            '0' . strval($lunchBreak->i) :
                            strval($lunchBreak->i)) . 'min';

                    $timeSlot[$startTime->format('Y-m-d')] = $startTime->format('H:i') . ' - ' . $endTime->format('H:i');
                    // if (!$form->getData()->getUser()->isForfaitHeure()) {
                    //     $valuedTimeFj = round($totalTime * $valuedQuotient);

                    //     if ($valuedTimeFj < 4) {
                    //         $valuedTime += 0.5;
                    //     } else {
                    //         $valuedTime += 1;
                    //     }
                    // } else {
                    //     $valuedTime += round($totalTime * $valuedQuotient);
                    // }
                    // Ajout des temps d'intervention jour et nuit
                    if (isset($form['tempsJour_0'])) {
                        $dayTime[$day] = $form->get('tempsJour_' . $i - 1)->getData();
                        $nightTime[$day] = $nightTime[$day] = $form->get('tempsNuit_' . $i - 1)->getData();
                    }
                }

                $astreinte->setTempsOperation($operationTimeArray);
                $astreinte->setTempsDejeuner($lunchTimeArray);
                $astreinte->setPlageHoraire($timeSlot);
                // $astreinte->setTempsValorise($valuedTime);
                $astreinte->setTempsInterventionJour($dayTime);
                $astreinte->setTempsInterventionNuit($nightTime);
                $astreinte->setState('validé-collaborateur-postop');
                $message = 'Vos temps d\'opérations ont bien été enregistrés.';
                $this->sendMailService->sendEmailToManager($astreinte, $this->urlGeneratorService->generate('app_astreinte_manager_index'));
            } else {
                if ($form->getData()->isOk()) {
                    $astreinte->setState('validé-collaborateur');
                    $this->sendMailService->sendEmailToRh($astreinte, $this->urlGeneratorService->generate('app_astreinte_rh_index'));
                } else {
                    $astreinte->setState('refus-collaborateur');
                    $message = 'Votre Demande d\'opération a bien été refusée.';
                    $this->sendMailService->sendEmailToManager($astreinte,  $this->urlGeneratorService->generate('app_dashboard'));
                }
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', $message)], Response::HTTP_SEE_OTHER);
        }

        return $this->render('collaborator/astreinte/edit.html.twig', [
            'astreinte' => $astreinte,
            'form' => $form,
            'astreinte_days' => $interval,
            'edit' => $edit
        ]);
    }
}
