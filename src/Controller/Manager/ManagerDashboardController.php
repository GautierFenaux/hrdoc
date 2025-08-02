<?php

namespace App\Controller\Manager;

use App\Entity\User;
use App\Repository\AstreinteRepository;
use App\Repository\CetRepository;
use App\Repository\ManagerRepository;
use App\Repository\RetourSurSiteRepository;
use App\Repository\TeletravailFormRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ManagerDashboardController extends AbstractController
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    #[Route('/manager/dashboard', name: 'app_manager_dashboard')]
    public function index(CetRepository $cetRepository, TeletravailFormRepository $teletravailFormRepository, ManagerRepository $managerRepository, 
    RetourSurSiteRepository $retourSurSiteRepository, AstreinteRepository $astreinteRepository): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        
        $user = $this->getUser();
        $manager = $managerRepository->findOneBy(['email' => $user->getEmail()]);
        
        $teletravailForms = $teletravailFormRepository->findByState($manager->getId(), 'attente-manager');
        $cets = $cetRepository->findBy(['manager' => $manager, 'state' => 'attente-manager']);
        
        $teletravailForms = $teletravailFormRepository->findByState($manager->getId(), 'attente-manager');

        return $this->render('manager/dashboard.html.twig', [
            'user' => $user,
            'retour_sur_site' => $retourSurSiteRepository->findByStatus($user->getId(), ['attente-rh', 'validé-rh']),
            'teletravail_forms' => $teletravailForms,
            'astreintes' => $astreinteRepository->findByStatus($user->getId(), ['validé-collaborateur-postop']),
            'cets' => $cets
        ]);
    }
   
}
