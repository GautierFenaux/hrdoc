<?php

namespace App\Controller\Rh;

use App\Entity\Astreinte;
use App\Form\AstreinteType;
use App\Service\SendMailService;
use App\Repository\AstreinteRepository;
use App\Service\UrlGeneratorService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rh/astreinte')]
class AstreinteRhController extends AbstractController
{

    public function __construct(
        private SendMailService $sendMailService,
        private UrlGeneratorService $urlGeneratorService,
    ) {}


    #[Route('/', name: 'app_astreinte_rh_index', methods: ['GET'])]
    public function index(AstreinteRepository $astreinteRepository): Response
    {
        return $this->render('rh/astreinte/index.html.twig', [
            'astreintes' => $astreinteRepository->findAll(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_astreinte_rh_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Astreinte $astreinte, EntityManagerInterface $entityManager): Response
    {
        $message = 'La demande d\'opération a bien été validée.';
        $increasedTime = [];
        $interval = $astreinte->getDebutAstreinte()->diff($astreinte->getFinAstreinte())->format('%a');
        $intervalArray = range(0, $interval);

        $edit = $astreinte->getState() == 'validé-manager-postop' ? 'isEditAfterOperation' : 'isEditRh';
        $form = $this->createForm(AstreinteType::class, $astreinte, [$edit => true, 'astreinte_days' => $interval]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Si la date de l'astreinte n'est pas encore passée alors c'est la validation avant astreinte
            if (date_diff($astreinte->getFinAstreinte(), new Datetime)->format("%R%") == '-') {
                if ($form->getData()->isOkRh()) {
                    $astreinte->setState('validé-rh');
                } else {
                    $astreinte->setState('refus-rh');
                    $message = 'La demande d\'opération a bien été réfusée.';
                }
            }
            if (isset($form['timeModification'])) {
                if ($astreinte->getState() === 'validé-manager-postop' && !$form->get('timeModification')->getData()) {
                    if ($form->get('increased_0')->getData() !== null) {
                        for ($i = 0; $i <= $interval; $i++) {
                            $increasedTime[$astreinte->getDebutAstreinte()->modify('+' . $intervalArray[$i] . 'days')->format('Y-m-d')] =
                            $form->get('increased_' . $i)->getData();
                        }
                        $astreinte->setTempsMajore($increasedTime);
                    }
                    $astreinte->setTempsValorise($form->getData()->getTempsValorise());
                    $astreinte->setState('validé-rh-postop');
                    $message = 'La saisie des temps post opération a bien été validée.';
                } else if ($form->get('timeModification')->getData()) {
                    $astreinte->setState('réouvert-rh-postop');
                    $message = 'La saisie des temps post opération a bien été réouverte.';
                }
            }
            $this->sendMailService->sendToCollaborator($astreinte, $this->urlGeneratorService->generate('app_dashboard'));
            $entityManager->flush();
            return $this->redirectToRoute('app_astreinte_rh_index', [$this->addFlash('success', $message)], Response::HTTP_SEE_OTHER);
        }
        return $this->render('rh/astreinte/edit.html.twig', [
            'astreinte' => $astreinte,
            'astreinte_days' => $interval,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_astreinte_rh_delete', methods: ['POST'])]
    public function delete(Request $request, Astreinte $astreinte, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $astreinte->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($astreinte);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_astreinte_index', [], Response::HTTP_SEE_OTHER);
    }
}
