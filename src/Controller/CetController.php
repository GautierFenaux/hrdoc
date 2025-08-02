<?php

namespace App\Controller;

use App\Entity\Cet;
use App\Form\CetType;
use DateTimeImmutable;
use App\Enum\StateEnum;
use App\Service\GetUserService;
use App\Service\SendMailService;
use App\Service\DownloadPdfService;
use App\Service\UrlGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/cet')]
class CetController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private SendMailService $sendMailService,
        private UrlGeneratorService $urlGeneratorService,
        private ParameterBagInterface $parameterBag,
        private GetUserService $getUserService,
        private DownloadPdfService $downloadPdfService,
    ) {}

    #[Route('/new', name: 'app_cet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUserService->getCurrentUser($this->getUser());
        if ($user->isEligibleCet() == true) {
            $cet = new Cet();
            $form = $this->createForm(CetType::class, $cet);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $cet->setUser($user);
                if ($form['alimentation']->getData() || $form['utilisation']->getData()) {
                    $cet->setState(StateEnum::PENDING_MANAGER_VALIDATION);
                    $this->sendMailService->sendEmailToManager($cet, $this->urlGeneratorService->generate('app_cet_manager_index'));
                } else {
                    $cet->setState(StateEnum::PENDING_HR_VALIDATION);
                    $this->sendMailService->sendEmailToRh($cet, $this->urlGeneratorService->generate('app_cet_rh_index'));
                }
                $cet->setCreatedAt(new DateTimeImmutable());
                $cet->setManager($user->getManager());
                $entityManager->persist($cet);
                $entityManager->flush();
                return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', "Votre demande de CET a bien été prise en compte.")], Response::HTTP_SEE_OTHER);
            }
        } else {
            return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', "Vous n'avez pas accès aux CET.")], Response::HTTP_SEE_OTHER);
        }
        return $this->render('collaborator/cet/new.html.twig', [
            'cet' => $cet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cet_show', methods: ['GET'])]
    public function show(Cet $cet): Response
    {
        return $this->render('collaborator/cet/show.html.twig', [
            'cet' => $cet,
        ]);
    }

    #[Route('/{id}/edit/{case}', name: 'app_cet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cet $cet, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUserService->getCurrentUser($this->getUser());
        if ($cet->getUser() != $user) {
            throw $this->createException();
        }
        $form = $this->createForm(CetType::class, $cet);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($cet->isRestitution()) {
                $cet->setState(StateEnum::PENDING_HR_VALIDATION);
                $this->sendMailService->sendEmailToRh($cet, $this->urlGeneratorService->generate('app_cet_rh_index'));
            } else {
                $cet->setState(StateEnum::PENDING_MANAGER_VALIDATION);
                $this->sendMailService->sendEmailToManager($cet, $this->urlGeneratorService->generate('app_cet_manager_index'));
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', "Votre demande de CET a bien été modifiée.")], Response::HTTP_SEE_OTHER);
        }
        return $this->render('collaborator/cet/edit.html.twig', [
            'cet' => $cet,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cet_delete', methods: ['POST'])]
    public function delete(Request $request, Cet $cet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cet->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($cet);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_cet_index', [], Response::HTTP_SEE_OTHER);
    }
}
