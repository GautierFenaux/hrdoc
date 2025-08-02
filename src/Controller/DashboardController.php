<?php

namespace App\Controller;

use DateTime;
use DateTimeImmutable;
use App\Service\GetUserService;
use App\Repository\CetRepository;
use App\Repository\AstreinteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RetourSurSiteRepository;
use App\Repository\TeletravailFormRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends AbstractController
{
    public function __construct(private  RequestStack $requestStack, private GetUserService $getUserService) {}
    #[Route('/', name: 'app_home')]
    public function redirectToDashboard(): RedirectResponse
    {
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(TeletravailFormRepository $teletravailFormRepository, CetRepository $cetRepository, RetourSurSiteRepository $retourSurSiteRepository, AstreinteRepository $astreinteRepository): Response
    {
        $user = $this->getUserService->getCurrentUser($this->getUser());

        $getEntitiesByStatuses = function (array $statuses, object $repository) use ($user) {
            return $repository->findByStatus($user->getId(), $statuses);
        };
        $isManager = false;
        // permet de gérer l'affichage de intro.js
        $teletravailForm = $getEntitiesByStatuses(['réouvert', 'attente-manager', 'validé-manager', 'refus-manager', 'attente-rh', 'validé-rh', 'refus-rh'], $teletravailFormRepository);
        $dateFinTeletravail = new DateTime();
        $dateFinTeletravail->setDate(date("Y"), 12, 31)->setTime(00, 00, 00);
        $formInDb = $teletravailFormRepository->findOneBy(['user' => $user->getId(), 'dateFinTeletravail' => $dateFinTeletravail]);
        if (in_array('ROLE_MANAGER', $user->getRoles(), true)) {
            $isManager = true;
        }
        return $this->render('collaborator/dashboard.html.twig', [
            'user' => $user,
            'is_manager' => $isManager,
            'arrived_after_january_2025' => new DateTimeImmutable('2025-01-01') < $user->getCreatedAt(),
            'is_first_connection' => $user->isFirstConnection() === true ? true : false,
            'teletravail_form' => $teletravailForm,
            'teletravail_form_count' => count($teletravailForm) > 0 ? $teletravailForm[0] : '',
            'cets' => $getEntitiesByStatuses(['réouvert', 'attente-manager', 'validé-manager', 'refus-manager', 'attente-rh'], $cetRepository),
            'retours_sur_site' => $getEntitiesByStatuses(['attente-rh', 'validé-rh', 'refus-rh'], $retourSurSiteRepository),
            'astreintes' => $getEntitiesByStatuses(['validé-rh', 'refus-rh', 'validé-manager', 'réouvert-manager-postop', 'réouvert-rh-postop', 'validé-collaborateur', 'validé-collaborateur-postop', 'validé-manager-postop'], $astreinteRepository),
            'validated_teletravail_forms' => $teletravailFormRepository->findByStatus($user->getId(), ['validé', 'refusé']),
            'validated_retours_sur_site' => $retourSurSiteRepository->findByStatus($user->getId(), ['validé', 'refusé']),
            'validated_cets' => $cetRepository->findByStatus($user->getId(), ['validé-rh', 'refus-rh']),
            'validated_astreintes' => $astreinteRepository->findByStatus($user->getId(), ['validé-rh-postop']),
            'existing_tt_current_year' => $formInDb ? true : false,
        ]);
    }

    #[Route('/update-connection-status', name: 'update_connection')]
    public function updateConnectionStatus(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUserService->getCurrentUser($this->getUser());
        $user->setFirstConnection(false);
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse('Mise à jour du status', 200);
    }

    #[Route('/dowload-kit', name: 'download_kit')]
    public function downloadKit(EntityManagerInterface $em): Response
    {
        $zipName = $this->getParameter('kernel.project_dir') . '/download/integration_kit.zip';
        try {
            $response =  new Response(
                file_get_contents($zipName),
                Response::HTTP_OK,
                [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="' . basename($zipName) . '"',
                    'Content-Length' => filesize($zipName)
                ]
            );
            $user = $this->getUserService->getCurrentUser($this->getUser());
            $user->setDownloadKit(true);
            $em->persist($user);
            $em->flush($user);
            return $response;
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            return new Response($errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
