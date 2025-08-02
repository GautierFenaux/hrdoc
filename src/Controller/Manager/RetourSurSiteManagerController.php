<?php

namespace App\Controller\Manager;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\RetourSurSite;
use App\Form\RetourSurSiteType;
use App\Service\GetUserService;
use App\Service\SendMailService;
use App\Repository\ManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RetourSurSiteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/manager/retour-sur-site')]
class RetourSurSiteManagerController extends AbstractController
{

    public function __construct(
        private GetUserService $getUserService,
        private SendMailService $sendMailService,
        private UrlGeneratorInterface $urlGeneratorInterface,
        private RequestStack $requestStack
    ) {}

    #[Route('/new', name: 'app_retour_sur_site_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,  ManagerRepository $managerRepository ): Response
    {
        $user = $this->getUserService->getCurrentUser($this->getUser());
        $manager =  $managerRepository->findOneByEmail($user->getEmail());

        $retourSurSite = new RetourSurSite();
        $form = $this->createForm(RetourSurSiteType::class, $retourSurSite, ['manager' => $manager]);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {

            $manager =  $managerRepository->findOneByEmail($user->getEmail());
            
            $retourSurSite->setManager($manager);
            $retourSurSite->setState('attente-rh');
            $retourSurSite->setCreatedAt(new DateTimeImmutable ('now'));
            $entityManager->persist($retourSurSite);
            $entityManager->flush();

            $urlToEditPage  = 'http://'. $request->getHost().$this->urlGeneratorInterface->generate('app_retour_sur_site_rh_index', ['id' => $retourSurSite->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);
            
            $urlToDashboard  = 'http://'. $request->getHost().$this->urlGeneratorInterface->generate('app_retour_sur_site_show', ['id' => $retourSurSite->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);

            // $this->sendMailService->sendToCollaborator($retourSurSite->getUser()->getEmail(), $retourSurSite, $urlToDashboard);

            $this->sendMailService->sendEmailToRh($retourSurSite, $urlToEditPage);

            return $this->redirectToRoute('app_manager_dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('retour_sur_site/new.html.twig', [
            'retour_sur_site' => $retourSurSite,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_retour_sur_site_show', methods: ['GET'])]
    public function show(RetourSurSite $retourSurSite): Response
    {
        return $this->render('retour_sur_site/show.html.twig', [
            'retour_sur_site' => $retourSurSite,
        ]);
    }

    #[Route('/', name: 'app_retour_sur_site_index', methods: ['GET'])]
    public function index(RetourSurSiteRepository $retourSurSiteRepository, ManagerRepository $managerRepository): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }     
        


        return $this->render('retour_sur_site_manager/index.html.twig', [
            'retours_sur_site' => $retourSurSiteRepository->findByStatusAndManager($managerRepository->findOneByEmail(
            $this->getUser()->getEmail())->getId(), 
            ['validé-rh', 'attente-rh','refus-rh']),
        ]);
    }


    #[Route('/{id}/edit', name: 'app_retour_sur_site_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RetourSurSite $retourSurSite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RetourSurSiteType::class, $retourSurSite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_retour_sur_site_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('retour_sur_site/edit.html.twig', [
            'retour_sur_site' => $retourSurSite,
            'form' => $form,
        ]);
    }

    // #[Route('/{id}', name: 'app_retour_sur_site_delete', methods: ['POST'])]
    // public function delete(Request $request, RetourSurSite $retourSurSite, EntityManagerInterface $entityManager): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$retourSurSite->getId(), $request->getPayload()->get('_token'))) {
    //         $entityManager->remove($retourSurSite);
    //         $entityManager->flush();
    //     }

    //     return $this->redirectToRoute('app_retour_sur_site_index', [], Response::HTTP_SEE_OTHER);
    // }
}
