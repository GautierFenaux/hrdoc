<?php

namespace App\Controller\Manager;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Astreinte;
use App\Form\AstreinteType;
use App\Service\SendMailService;
use Symfony\UX\Turbo\TurboBundle;
use App\Service\UrlGeneratorService;
use App\Repository\ManagerRepository;
use App\Repository\AstreinteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/manager/astreinte')]
class AstreinteManagerController extends AbstractController
{
    public function __construct(private UrlGeneratorService $urlGeneratorService, private SendMailService $sendMailService) {}

    #[Route('/', name: 'app_astreinte_manager_index', methods: ['GET'])]
    public function index(AstreinteRepository $astreinteRepository): Response
    {
        return $this->render('manager/astreinte/index.html.twig', [
            'astreintes' => $astreinteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_astreinte_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ManagerRepository $managerRepository): Response
    {
        $astreinte = new Astreinte();

        $manager = $managerRepository->findOneByEmail($this->getUser()->getEntry()->getAttributes()['mail'][0]);
        $form = $this->createForm(AstreinteType::class, $astreinte, ['manager' => $manager]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $astreinte->setCreatedAt(new DateTimeImmutable());
            $astreinte->setManager($manager);
            $astreinte->setState('validé-manager');
            $entityManager->persist($astreinte);
            $entityManager->flush();

            $this->sendMailService->sendToCollaborator($astreinte, $this->urlGeneratorService->generate('app_astreinte_edit', ['id' => $astreinte->getId()]));

            return $this->redirectToRoute('app_manager_dashboard', [$this->addFlash('success', 'La demande d\'opération concernant ' . $astreinte->getUser()->getName() . ' ' . $astreinte->getUser()->getsurName() . ' a bien été envoyée.')], Response::HTTP_SEE_OTHER);
        }

        return $this->render('manager/astreinte/new.html.twig', [
            'astreinte' => $astreinte,
            'form' => $form,
        ]);
    }
    #[Route('/{id}/edit', name: 'app_astreinte_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Astreinte $astreinte, EntityManagerInterface $entityManager): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        if ($astreinte->getManager()->getEmail() != $loggedUser->getEmail()) {
            return new Response(json_encode('Vous n\'êtes pas autorisé à modifier cette élément.'));
        }
        $toastMessage = 'La saisie post opérations de ' .  $astreinte->getUser()->getName() . ' ' . $astreinte->getUser()->getsurName();
        if ($request->query->get('type_of_validation') === 'refuse') {
            $astreinte->setState('refus-manager-postop');
            $toastMessage .= ' a bien été refusée.';
        } else if ($request->query->get('type_of_validation') === 'reopen') {
            $astreinte->setState('réouvert-manager-postop');
            $toastMessage .= ' a bien été réouverte.';
        } else {
            $astreinte->setState('validé-manager-postop');
            $toastMessage .= ' a bien été validée.';
            // if (isset(json_decode($request->getContent(), true)['isValidated'])) {
            //     $astreinte->setState('refus-manager-postop');
            //     $entityManager->flush();
            //     $this->sendMailService->sendToCollaborator($astreinte, $this->urlGeneratorService->generate('app_dashboard'));
            //     return new Response(json_encode('La saisie post opérations de ' . $userName . ' a été refusée.'));
            // }
        }
        $this->sendMailService->sendToCollaborator($astreinte, $this->urlGeneratorService->generate('app_astreinte_rh_edit', ['id' => $astreinte->getId()]));
        $entityManager->persist($astreinte);
        $entityManager->flush();
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('components/TableExt/TableRow.stream.html.twig', [
            'entity' => $astreinte,
            'toast_message' => $toastMessage,
            'role' => 'manager'
        ]);
        // $this->sendMailService->sendEmailToRh($astreinte, $this->urlGeneratorService->generate('app_astreinte_rh_index'));
        // return new Response(json_encode('La saisie des temps post opérations de ' . $userName . ' a bien été validée.'));
    }

    #[Route('/{id}', name: 'app_astreinte_manager_show', methods: ['GET'])]
    public function show(Astreinte $astreinte): Response
    {
        return $this->render('manager/astreinte/show.html.twig', [
            'astreinte' => $astreinte,
        ]);
    }

    #[Route('/{id}/reopen', name: 'app_astreinte_manager_reopen', methods: ['POST'])]
    public function rhReopen(Request $request, Astreinte $astreinte, EntityManagerInterface $entityManager): Response
    {
        $astreinte->setState('réouvert-manager-postop');
        $entityManager->persist($astreinte);
        $entityManager->flush();
        $this->sendMailService->sendToCollaborator($astreinte, $this->urlGeneratorService->generate('app_dashboard'));
        return new Response(json_encode('Demande d\'opérations de ' . $astreinte->getUser()->getName() . ' ' . $astreinte->getUser()->getsurName() . ' réouverte.'));
    }
}
