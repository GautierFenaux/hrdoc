<?php

namespace App\Controller\Rh;

use App\Entity\User;
use App\Entity\RetourSurSite;
use App\Form\RetourSurSiteType;
use App\Service\GetUserService;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RetourSurSiteRepository;
use App\Service\SendMailService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rh/retour-sur-site')]
class RetourSurSiteRhController extends AbstractController
{

    public function __construct(
        private GetUserService $getUserService,
        private UrlGeneratorInterface $urlGeneratorInterface,
        private SendMailService $sendMailService
    ) {}
    

    // #[Route('/{id}', name: 'app_retour_sur_site_show', methods: ['GET'])]
    // public function show(RetourSurSite $retourSurSite): Response
    // {
    //     return $this->render('retour_sur_site/show.html.twig', [
    //         'retour_sur_site' => $retourSurSite,
    //     ]);
    // }

    #[Route('/', name: 'app_retour_sur_site_rh_index', methods: ['GET'])]
    public function index(RetourSurSiteRepository $retourSurSiteRepository): Response
    {
        return $this->render('retour_sur_site/index.html.twig', [
            'retours_sur_site' => $retourSurSiteRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_retour_sur_site_rh_edit', methods: ['POST'])]
    public function edit(Request $request, RetourSurSite $retourSurSite, EntityManagerInterface $entityManager): Response
    {

        // $loggedUser = $this->getUser();
        // if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
        //     throw $this->createAccessDeniedException();
        // }
        
        $user =  $this->getUserService->getCurrentUser($this->getUser());

        // isValidated est à false par défaut voir ajax.js (méthode refuseValidation)
        if (isset(json_decode($request->getContent(), true)['isValidated'])) {
            
            $retourSurSite->setState('refus-rh');
            $retourSurSite->setSignatureRh(true);
            
            $entityManager->persist($retourSurSite);
            $entityManager->flush();
            
            $urlToEditPage  = 'http://' . $request->getHost() . $this->urlGeneratorInterface->generate('app_dashboard', [], UrlGeneratorInterface::ABSOLUTE_PATH);
            $this->sendMailService->sendEditEmailToCollaborator($retourSurSite, $urlToEditPage);
            return new Response(json_encode('Demande de retour sur site de concernant ' .  $retourSurSite->getUser()->getName() . ' ' . $retourSurSite->getUser()->getsurName() . ' refusée.'));
        }

        $retourSurSite->setState('validé-rh');
        $retourSurSite->setSignatureRh(true);
        $entityManager->persist($retourSurSite);
        $entityManager->flush();


        $urlToEditPage  = 'http://' . $request->getHost() . $this->urlGeneratorInterface->generate('app_dashboard', [], UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->sendMailService->sendEditEmailToCollaborator($retourSurSite, $urlToEditPage);

        return new Response(json_encode('Demande de retour sur site concernant ' .  $retourSurSite->getUser()->getName() . ' ' . $retourSurSite->getUser()->getsurName() . ' validée.'));

        return $this->render('retour_sur_site/edit.html.twig', [
            'retour_sur_site' => $retourSurSite,
        ]);
    }

    #[Route('/{id}', name: 'app_retour_sur_site_delete', methods: ['POST'])]
    public function delete(Request $request, RetourSurSite $retourSurSite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$retourSurSite->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($retourSurSite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_retour_sur_site_index', [], Response::HTTP_SEE_OTHER);
    }
}
