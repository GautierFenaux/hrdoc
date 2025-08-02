<?php

namespace App\Controller\Rh;

use App\Entity\Cet;
use App\Entity\User;
use App\Enum\StateEnum;
use App\Service\GetUserService;
use App\Service\SendMailService;
use App\Repository\CetRepository;
use App\Service\CreatePdfService;
use Symfony\UX\Turbo\TurboBundle;
use App\Repository\UserRepository;
use App\Service\UrlGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/rh/cet')]
class CetRhController extends AbstractController
{
    public function __construct(private UrlGeneratorService $urlGeneratorService, private SendMailService $sendMailService, private RequestStack $requestStack, private GetUserService $getUserService, private ParameterBagInterface $parameterBag, private MailerInterface $mailer, private EntityManagerInterface $entityManager, private CreatePdfService $createPdfService) {}

    #[Route('/', name: 'app_cet_rh_index', methods: ['GET'])]
    public function index(CetRepository $cetRepository): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        return $this->render('rh/cet/index.html.twig', [
            'cets_restitution' => $cetRepository->findBy(['restitution' => 1]),
            'cets_utilisation' => $cetRepository->findBy(['utilisation' => 1]),
            'cets_alimentation' => $cetRepository->findBy(['alimentation' => 1]),
            'pending_cets_alimentation' => count($cetRepository->findBy(['alimentation' => 1, 'state' => 'attente-rh'])) + count($cetRepository->findBy(['alimentation' => 1, 'state' => 'refus-manager'])),
            'pending_cets_utilisation' => count($cetRepository->findBy(['utilisation' => 1, 'state' => 'attente-rh'])) + count($cetRepository->findBy(['utilisation' => 1, 'state' => 'refus-manager'])),
            'pending_cets_restitution' => count($cetRepository->findBy(['restitution' => 1, 'state' => 'attente-rh'])) + count($cetRepository->findBy(['restitution' => 1, 'state' => 'refus-manager'])),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cet_rh_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cet $cet, EntityManagerInterface $entityManager): Response
    {
        $toastMessage = 'Demande CET de ' .  $cet->getUser()->getName() . ' ' . $cet->getUser()->getsurName();
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        function getType(Cet $cet)
        {
            $cetTypes = ['alimentation', 'utilisation', 'restitution'];
            for ($i = 0; $i <= 2; $i++) {
                $getter = 'is' . ucfirst($cetTypes[$i]);
                if ($cet->$getter() == true) {
                    return $cetTypes[$i];
                }
            }
        }
        $html = $this->renderView('_pdf/cet_' . getType($cet) . '_pdf.html.twig', [
            'cet' => $cet,
            'signature' => ucfirst($cet->getUser()->getName()) . ' ' . ucfirst(strtolower($cet->getUser()->getSurname())),
            'logo' => 'data:png;base64,' . base64_encode(file_get_contents($this->parameterBag->get('kernel.project_dir') . '/public/assets/images/hrdoc_logo.png')),
        ]);
        $cet->setCommentaireDrh($request->query->get('comment'));
        if ($request->query->get('type_of_validation') === 'refuse') {
            $cet->setState(StateEnum::REFUSED_HR);
            $cet->setAvisDrh(false);
            $pdfPath = $this->createPdfService->create('cet', $html);
            $cet->setLocation($pdfPath);
            $this->sendMailService->sendToCollaborator($cet, $this->urlGeneratorService->generate('app_dashboard'), $pdfPath);
            $toastMessage .= ' refusée.';
        } else if ($request->query->get('type_of_validation') === 'reopen') {
            $cet->setState(StateEnum::REOPEN);
            $cet->setAvisDrh(false);
            $this->sendMailService->sendToCollaborator($cet, $this->urlGeneratorService->generate('app_dashboard'));
            $toastMessage .= ' réouverte.';
        } else {
            $cet->setState(StateEnum::VALITED_HR);
            $cet->setAvisDrh(true);
            if ($cet->isUtilisation()) {
                $cet->setSolde($cet->getNbJours() - $cet->getNbJoursADebiter());
            }
            $toastMessage .= ' validée.';
            $pdfPath = $this->createPdfService->create('cet', $html);
            $cet->setLocation($pdfPath);
            $this->sendMailService->sendToCollaborator($cet, $this->urlGeneratorService->generate('app_dashboard'), $pdfPath);
        }
        $entityManager->persist($cet);
        $entityManager->flush();
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('components/TableExt/TableRow.stream.html.twig', [
            'entity' => $cet,
            'toast_message' => $toastMessage,
            'role' => 'rh',
        ]);
    }
    #[Route('/reminder', name: 'app_cet_rh_reminder', methods: ['GET'])]
    public function showReminder(UserRepository $userRepository): Response
    {
        $collaborators = $userRepository->findBy(['eligibleCet' => true]);
        $managerToRemind = [];
        foreach ($collaborators as $collaborator) {
            $cets = $collaborator->getCet();
            $collabInfos = $collaborator->getId() . '-' . str_replace('-', ' ', $collaborator->getName()) . ' ' . $collaborator->getSurname();
            if (!$cets->isEmpty()) {
                foreach ($cets as $cet) {
                    if ($cet->getState() === StateEnum::PENDING_MANAGER_VALIDATION) {
                        $managerToRemind[$collabInfos][] = $cet->getUser()->getManager();
                    }
                }
            }
        }
        return $this->render('rh/cet/reminders.html.twig', [
            'managers_to_remind' => $managerToRemind,
        ]);
    }

    // TODO : factoriser en créant un service
    #[Route('/reminder/{id}/{status}', name: 'app_cet_reminder', methods: ['GET'])]
    public function manualReminder(UserRepository $userRepository, string $id, string $status, RequestStack $requestStack, Request $request): Response
    {
        $loggedUser = $requestStack->getSession()->get('user');
        if (!$loggedUser instanceof User || !in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        $id = strtok($id, '-');
        $collaborator = $userRepository->findOneById($id);
        $email = (new TemplatedEmail())
            ->from('no-reply-Hrdoc@hrdoc.fr');
        if ($status == 'attente-manager') {
            $email->subject('Relance signature demande de CET')
                ->to($collaborator->getManager()->getEmail())
                ->htmlTemplate('emails/reminders/cet/reminder_manager_cet.html.twig')
                ->context(
                    [
                        'collaborator' => $collaborator,
                        'url' => $this->urlGeneratorService->generate('app_cet_manager_index'),
                    ]
                );
        }
        $this->mailer->send($email);
        return new Response('', 200);
    }
}
