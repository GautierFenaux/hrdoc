<?php

namespace App\Controller\Manager;

use App\Entity\Cet;
use App\Entity\User;
use App\Enum\StateEnum;
use App\Service\GetUserService;
use App\Service\SendMailService;
use App\Repository\CetRepository;
use Symfony\UX\Turbo\TurboBundle;
use App\Service\UrlGeneratorService;
use App\Repository\ManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/manager/cet')]
class CetManagerController extends AbstractController
{
    public function __construct(private UrlGeneratorService $urlGeneratorService, private SendMailService $sendMailService, private RequestStack $requestStack, private GetUserService $getUserService) {}
    #[Route('/', name: 'app_cet_manager_index', methods: ['GET'])]
    public function index(CetRepository $cetRepository, ManagerRepository $managerRepository): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        $manager = $managerRepository->findOneBy(['email' => $loggedUser->getEmail()]);
        return $this->render('manager/cet/index.html.twig', [
            'cets_alimentation' => $cetRepository->findBy(['manager' => $manager, 'alimentation' => 1]),
            'cets_utilisation' => $cetRepository->findBy(['manager' => $manager, 'utilisation' => 1]),
            'pending_cets_alimentation' => $cetRepository->findBy(['manager' => $manager, 'alimentation' => 1, 'state' => 'attente-manager']),
            'pending_cets_utilisation' => $cetRepository->findBy(['manager' => $manager, 'utilisation' => 1, 'state' => 'attente-manager']),
            'pending_cets_restitution' => $cetRepository->findBy(['manager' => $manager, 'restitution' => 1, 'state' => 'attente-manager']),
        ]);
    }
    #[Route('/{id}/edit', name: 'app_cet_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cet $cet, EntityManagerInterface $entityManager, CetRepository $cetRepository): Response
    {
        $loggedUser = $this->getUser();  
        $toastMessage = 'La demande de CET de ' .  $cet->getUser()->getName() . ' ' . $cet->getUser()->getsurName() . ' a été validée.';
        if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        if ($cet->getManager()->getEmail() != $loggedUser->getEmail()) {
            return new Response(json_encode('Vous n\'êtes pas autorisé à modifier cette élément.'));
        }
        if ($request->query->get('type_of_validation') === 'refuse') {
            $cet->setState(StateEnum::REFUSED_MANAGER);
            $cet->setAvisSupHierarchique(false);
            $toastMessage = 'La demande de CET de ' .  $cet->getUser()->getName() . ' ' . $cet->getUser()->getsurName() . ' a été refusée.';
            $this->sendMailService->sendEmailToRh($cet, $this->urlGeneratorService->generate('app_dashboard'));
        } else {
            $cet->setState(StateEnum::PENDING_HR_VALIDATION);
            $cet->setAvisSupHierarchique(true);
            $this->sendMailService->sendEmailToRh($cet, $this->urlGeneratorService->generate('app_cet_rh_index'));
        }
        $cet->setCommentaireSupHierarchique($request->query->get('comment'));
        $entityManager->flush();
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('components/TableExt/TableRow.stream.html.twig', [
            'entity' => $cet,
            'toast_message' => $toastMessage,
            'role' => 'manager',
        ]);
    }
}
