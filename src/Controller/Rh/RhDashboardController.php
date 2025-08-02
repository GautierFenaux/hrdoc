<?php

namespace App\Controller\Rh;

use App\Entity\User;
use App\Enum\StateEnum;
use App\Repository\CetRepository;
use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\AstreinteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RetourSurSiteRepository;
use App\Repository\TeletravailFormRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RhDashboardController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager, private RequestStack $requestStack) {}

    #[Route('/rh/dashboard', name: 'app_rh_dashboard')]
    public function index(TeletravailFormRepository $teletravailFormRepository, RequestStack $requestStack, UserRepository $userRepository, CetRepository $cetRepository, RetourSurSiteRepository $retourSurSiteRepository, AstreinteRepository $astreinteRepository, ChartBuilderInterface $chartBuilder): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }

        $user = $this->getUser();

        // TODO: Récupérer les statuts et l'année courante pour être plus précis, faire requête DQL.
        $pendingRhTT = count($teletravailFormRepository->findBy(['state' => 'validé-manager'])) + count($teletravailFormRepository->findBy(['state' => 'attente-rh']));

        $pendingRhCET = count($cetRepository->findBy(['state' => 'attente-rh']));

        $pendingRhRss = count($retourSurSiteRepository->findBy(['state' => 'attente-rh']));

        $pendingRhAstreinte = count($astreinteRepository->findBy(['state' => 'validé-collaborateur'])) + count($astreinteRepository->findBy(['state' => 'validé-manager-postop']));

        $pendingStates = [
            StateEnum::PENDING_MANAGER_VALIDATION,
            StateEnum::VALITED_MANAGER,
            StateEnum::REFUSED_MANAGER,
            StateEnum::REOPEN,
            StateEnum::PENDING_HR_VALIDATION,
            StateEnum::REFUSED_HR,
            StateEnum::VALITED_HR,
        ];

        $terminatedStates = [
            StateEnum::VALITED,
            StateEnum::REFUSED,
        ];

        $pendingStateStrings = array_map(fn($e) => $e->value, $pendingStates);
        $terminatedStateStrings = array_map(fn($e) => $e->value, $terminatedStates);
        $currentYear = (int) date('Y');

        $teleworkingChart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $teleworkingChart->setData([
            'labels' => ['validé', 'en cours', 'non validé'],
            'datasets' => [[
                'label' => 'Demandes de télétravail',
                'backgroundColor' => ['#93ecc3', '#F5DA59', '#FFBBAB'],
                'data' => [
                    $teletravailFormRepository->countByYearAndStates($currentYear, $terminatedStateStrings),
                    $teletravailFormRepository->countByYearAndStates($currentYear, $pendingStateStrings),
                    $userRepository->countWithoutTeletravailForYear($currentYear),
                ],
            ]],
        ]);
        $teleworkingChart->setOptions([
            'responsive' => true,
            'width' => 225,               // Width in pixels
            'height' => 225,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ]);


        $cetChart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $cetChart->setData([
            'labels' => ['alimentation', 'utilisation', 'restitution'],
            'datasets' => [[
                'label' => 'Demande de cet',
                'backgroundColor' => ['#93ecc3', '#F5DA59', '#FFBBAB'],
                'data' => [
                    count($cetRepository->findByDateAndType('alimentation')),
                    count($cetRepository->findByDateAndType('utilisation')),
                    count($cetRepository->findByDateAndType('restitution')),
                ],
            ]],
        ]);
        $cetChart->setOptions([
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ]);
        return $this->render('rh/dashboard.html.twig', [
            'pending_rh_rss' => $pendingRhRss,

            // Pour afficher dans la bulle de notification
            'pending_rh_teletravail' => $pendingRhTT,
            'waiting_rh_cet' => $pendingRhCET,
            'pending_rh_astreinte' => $pendingRhAstreinte,
            'user' => $user,

            // Permet d'afficher la pastille avec le nombre de cet à traiter
            'cets_alimentation' => count($cetRepository->findBy(['state' => 'refus-manager', 'alimentation' => 1])) + count($cetRepository->findBy(['state' => 'attente-rh', 'alimentation' => 1])),
            'cets_utilisation' => count($cetRepository->findBy(['state' => 'refus-manager', 'utilisation' => 1])) + count($cetRepository->findBy(['state' => 'attente-rh', 'utilisation' => 1])),
            'cets_restitution' => count($cetRepository->findBy(['state' => 'refus-manager', 'restitution' => 1])) + count($cetRepository->findBy(['state' => 'attente-rh', 'restitution' => 1])),

            'teleworking_chart' => $teleworkingChart,
            'cet_chart' => $cetChart,
        ]);
    }
}
