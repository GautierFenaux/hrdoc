<?php

namespace App\Controller\Rh;

use DateTime;

use App\Entity\User;
use App\Enum\StateEnum;
use App\Entity\TeletravailForm;
use App\Service\GetUserService;
use App\Service\SendMailService;
use App\Form\TeletravailFormType;
use Symfony\UX\Turbo\TurboBundle;
use App\Repository\UserRepository;
use App\Service\UrlGeneratorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use App\Repository\TeletravailFormRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/rh/teletravailform')]
class TeletravailFormRhController extends AbstractController
{

    private $requestStack;
    private $sendMailService;


    public function __construct(RequestStack $requestStack, SendMailService $sendMailService, private UrlGeneratorService $urlGeneratorService, private GetUserService $getUserService, private MailerInterface $mailer)
    {

        $this->requestStack = $requestStack;
        $this->sendMailService = $sendMailService;
    }

    #[Route('/', name: 'app_teletravailform_rh_index', methods: ['GET'])]
    public function index(TeletravailFormRepository $teletravailFormRepository): Response
    {

        return $this->render('rh/teletravail_form/index.html.twig', [
            'teletravail_forms' => $teletravailFormRepository->findAll(),
        ]);
    }

    // Récupérer le path du pdf du formulaire de télétravail en cours via l'id.
    #[Route('/pdf/{id}/{file}', name: 'app_display_pdf', methods: ['GET'])]
    public function displayPdf(Request $request, TeletravailForm $teletravailForm, EntityManagerInterface $entityManager, $file): Response
    {

        $file_name = $file == 'attestation_honneur' ? explode('/', implode(parse_url($teletravailForm->getAttestationHonneur()))) :
            explode('/', implode(parse_url($teletravailForm->getAttestationAssurance())));

        $splFileInfo = $file == 'attestation_honneur' ?  $teletravailForm->getAttestationHonneur() :  $teletravailForm->getAttestationAssurance();

        return $this->file($splFileInfo, end($file_name), ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/{id}/show', name: 'app_teletravailform_rh_show', methods: ['GET'])]
    public function show(Request $request, TeletravailForm $teletravailForm): Response
    {
        $form = $this->createForm(TeletravailFormType::class, $teletravailForm, [
            'user_roles'  => $teletravailForm->getUser()->getRoles(),
            'request' => $request,
            'current_user' => $teletravailForm->getUser(),
        ]);

        return $this->render('rh/teletravail_form/show.html.twig', [
            'teletravail_form' => $teletravailForm,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_teletravailform_rh_edit', methods: ['GET', 'POST'])]
    public function rhEdit(Request $request, TeletravailForm $teletravailForm, EntityManagerInterface $entityManager, UserInterface $user): Response
    {

        // Permet de passer l'objet request dans les options du formulaire pour gérer l'affichage des champs en fonction des routes.
        $request = $this->requestStack->getCurrentRequest();
        $user =  $this->getUser();

        $form = $this->createForm(TeletravailFormType::class, $teletravailForm, [
            'user_roles'  => $user->getRoles(),
            'request' => $request,
            'current_user' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form['avisDRH']->getData() == true) {
                $teletravailForm->setState(StateEnum::VALITED_HR);
            } else {
                $teletravailForm->setState(StateEnum::REFUSED_HR);
                $teletravailForm->setCommentaireDRH($form['commentaireDRH']->getData());
            }

            $teletravailForm->setReceptionDemande(new DateTime());
            $entityManager->persist($teletravailForm);
            $entityManager->flush();

            // Fix car pb en prod rédirige sur une erreur 500 si utilisateur non-connecté.
            $urlToEditPage  = 'http://' . $request->getHost();
            // . $this->urlGeneratorInterface->generate('app_teletravail_form_edit', ['id' => $teletravailForm->getId()], UrlGeneratorInterface::ABSOLUTE_PATH);

            $this->sendMailService->sendToCollaborator($teletravailForm, $urlToEditPage);
            $this->addFlash('success', 'Votre validation pour la demande de ' .  $teletravailForm->getUser()->getName() . ' ' . $teletravailForm->getUser()->getsurName() . ' a bien été prise en compte.');
            return $this->redirectToRoute('app_teletravailform_rh_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rh/teletravail_form/edit.html.twig', [
            'teletravail_form' => $teletravailForm,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/reopen', name: 'app_teletravailform_rh_reopen', methods: ['POST'])]
    public function rhReopen(Request $request, TeletravailForm $teletravailForm, EntityManagerInterface $entityManager): Response
    {

        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
            return new Response(json_encode('Vous ne disposez pas des droits nécessaires pour accéder à cette route'));
        }

        $teletravailForm->setState(StateEnum::REOPEN);
        $teletravailForm->setCommentaireDRH(json_decode($request->getContent(), true)['comment']);

        $entityManager->persist($teletravailForm);
        $entityManager->flush();

        $this->sendMailService->sendToCollaborator($teletravailForm, $this->urlGeneratorService->generate('app_teletravailform_reopen', ['id' => $teletravailForm->getId()]));

        return new Response(json_encode('Déclaration de ' .  $teletravailForm->getUser()->getName() . ' ' . $teletravailForm->getUser()->getsurName() . ' réouverte.'));
    }


    #[Route('/{id}', name: 'app_teletravailform_rh_delete', methods: ['POST'])]
    public function delete(Request $request, TeletravailForm $teletravailForm, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $teletravailForm->getId(), $request->request->get('_token'))) {
            $entityManager->remove($teletravailForm);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_teletravailform_rh_index', [$this->addFlash('success', 'La demande de télétravail de ' .
            $teletravailForm->getUser()->getName() . ' ' .
            $teletravailForm->getUser()->getSurname() . ' a bien été supprimée')], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reminder', name: 'app_teletravail_form_rh_reminder', methods: ['GET'])]
    public function showReminder(UserRepository $userRepository): Response
    {
        $collaborators = $userRepository->findBy(['eligibleTT' => true]);
        $emptyTTForCurrentYear = [];
        $existingTTForCurrentYear = [];

        // Peut-être implémenter une logique en passant par le teletravailFormRepository ?
        foreach ($collaborators as $collaborator) {

            $teletravails = $collaborator->getTeletravailForms();
            $collabInfos = $collaborator->getId() . '-' . str_replace('-', ' ', $collaborator->getName()) . ' ' . $collaborator->getSurname();

            if (!$teletravails->isEmpty()) {
                foreach ($teletravails as $teletravail) {
                    // Check si tt est pour l'année en cours
                    if ($teletravail->getACompterDu()->format('Y') == date('Y')) {

                        // false et ne sera pas affiché dans la vue des relances, sauf si état, obligé de faire comme ça car twig n'arrive pas à checker si string dans array...
                        // Si état ok pour relance alors on push l'état pour l'afficher dans la vue, sinon statut not ok pr relance.
                        if ($teletravail->getState() === StateEnum::VALITED_HR || $teletravail->getState() === StateEnum::PENDING_MANAGER_VALIDATION) {
                            $existingTTForCurrentYear[$collabInfos][] = $teletravail->getState()->value;
                            if ($teletravail->getState()->value === StateEnum::PENDING_MANAGER_VALIDATION) {
                                $existingTTForCurrentYear[$collabInfos][] = $collaborator->getManager()->getRelanceTeletravail();
                            } else {
                                $existingTTForCurrentYear[$collabInfos][] = $collaborator->getRelanceTeletravail();
                            }
                        } else {
                            $existingTTForCurrentYear[$collabInfos][] = true;
                        }
                    } else {
                        // true si formulaire n'est pas pour l'année en cours, collab ne sera pas affiché dans la vue des relances
                        $emptyTTForCurrentYear[$collabInfos][] = true;
                        $emptyTTForCurrentYear[$collabInfos][] = $collaborator->getRelanceTeletravail();
                    }
                }
            } else {
                // true si aucun formulaire
                $emptyTTForCurrentYear[$collabInfos][] = true;
                $emptyTTForCurrentYear[$collabInfos][] = $collaborator->getRelanceTeletravail();
            }
            // Supprime les tt qui ne doivent pas être relancés
            if (array_key_exists($collabInfos, $existingTTForCurrentYear)) {
                foreach ($existingTTForCurrentYear[$collabInfos] as $existingTT) {
                    // On cherche si il y a d'autres formulaires existants via true, on les supprime si oui, pour ne garder que les formulaires à relancer.
                    if (($key = array_search('true', $existingTTForCurrentYear[$collabInfos])) !== false) {
                        unset($existingTTForCurrentYear[$collabInfos][$key]);
                        $existingTTForCurrentYear[$collabInfos] = array_values($existingTTForCurrentYear[$collabInfos]);
                    }
                    unset($emptyTTForCurrentYear[$collabInfos]);
                }
            }
        }
        // dd($existingTTForCurrentYear);
        return $this->render('rh/teletravail_form/index_reminders.html.twig', [
            'existing_teletravails' => $existingTTForCurrentYear,
            'empty_teletravails' => $emptyTTForCurrentYear,
        ]);
    }

    // TODO : factoriser en créant un service
    #[Route('/reminder/{id}/{status}', name: 'app_teletravail_form_reminder', methods: ['GET'])]
    public function manualReminder(UserRepository $userRepository, string $id, string $status, RequestStack $requestStack, Request $request, EntityManagerInterface $entityManager): Response
    {
        $loggedUser = $requestStack->getSession()->get('user');
        if (!$loggedUser instanceof User || !in_array('ROLE_ADMIN', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        $id = strtok($id, '-');

        $collaborator = $userRepository->findOneById($id);
        $toastMessage = $collaborator->getName() . ' ' . $collaborator->getSurname() . " a bien été relancé(e)";

        function setEmail($emailAdress, string $template, string $subject, string $route, User $collaborator)
        {

            return (new TemplatedEmail())
                ->from('no-reply-Hrdoc@hrdoc.fr')
                ->subject($subject)
                ->to($emailAdress)
                ->htmlTemplate($template)
                ->context(
                    [
                        'collaborator' => $collaborator,
                        'url' => $route,
                    ]
                );
        }

        if ($status == 'non-complété') {
            $email = setEmail($collaborator->getEmail(), 'emails/reminders/reminder_teletravail.html.twig', 'Rappel demande de télétravail', $this->urlGeneratorService->generate('app_teletravail_form_new'), $collaborator);
        }

        if ($status == 'attente-manager') {
            $email = setEmail($collaborator->getManager()->getEmail(), 'emails/reminders/reminder_manager_teletravail.html.twig', 'Rappel demande de télétravail', $this->urlGeneratorService->generate('app_teletravail_form_manager_index'), $collaborator);
            $toastMessage = 'Le manager de ' . $toastMessage;
        }

        if ($status == 'validé-rh') {
            $email = setEmail($collaborator->getEmail(), 'emails/reminders/reminder_final_teletravail.html.twig', 'Signature finale demandée', $this->urlGeneratorService->generate('app_dashboard'), $collaborator);
        }
        $this->mailer->send($email);



        // Faire en fonction du manager également
        if ($status === 'attente-manager') {
            $collaborator->getManager()->setRelanceTeletravail(new DateTime());
            $entityManager->persist($collaborator);
            $entityManager->flush();
        } else {
            $collaborator->setRelanceTeletravail(new DateTime());
            $entityManager->persist($collaborator);
            $entityManager->flush();
        }

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('components/TableExt/TableRow.stream.html.twig', [
            'entity' => $collaborator,
            'status' => $status,
            'toast_message' => $toastMessage,
            'role' => 'rh',
        ]);
    }
}
