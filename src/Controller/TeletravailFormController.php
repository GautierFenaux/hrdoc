<?php

namespace App\Controller;

use DateTime;
use Dompdf\Dompdf;

use DateTimeImmutable;
use App\Enum\StateEnum;
use App\Entity\TeletravailForm;
use App\Service\GetUserService;
use App\Service\SendMailService;
use App\Form\TeletravailFormType;
use App\Service\CreatePdfService;
use App\Repository\UserRepository;
use App\Service\DownloadPdfService;
use App\Service\UrlGeneratorService;
use App\Repository\ManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/teletravailform')]
class TeletravailFormController extends AbstractController
{


    public function __construct(
        private RequestStack $requestStack,
        private SendMailService $sendMailService,
        private UrlGeneratorService $urlGeneratorService,
        private ParameterBagInterface $parameterBag,
        private GetUserService $getUserService,
        private DownloadPdfService $downloadPdfService,
        private CreatePdfService $createPdfService,
    ) {
    }

    #[Route('/new', name: 'app_teletravail_form_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserInterface $user): Response
    {
        // Générer ici le lien et le mail vers le formulaire à éditer vers le manager du collaborateur ayant compléter le formulaire.
        $user = $this->getUserService->getCurrentUser($this->getUser());
        if ($user->isEligibleTT() == true) {
                $teletravailForm = new TeletravailForm();
                $request = $this->requestStack->getCurrentRequest();
                $form = $this->createForm(TeletravailFormType::class, $teletravailForm, [
                    'user_roles'  => $user->getRoles(),
                    'request' => $request,
                ]);
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $date = new DateTime();
                     if ($form['aCompterDu']->getData() === NULL) {
                        $this->addFlash('error', 'Veuillez sélectionner une date avant de soumettre le formulaire.');
                        return $this->redirectToRoute('app_teletravail_form_new');
                    }
                    $date->setDate($form['aCompterDu']->getData()->format('Y'), 12, 31);
                    $date->setTime(00, 00, 00);
                    $teletravailForm->setDateFinTeletravail($date);
                    $attestationAssurance = $form['attestationAssurance']->getData();
                    $attestationAssuranceName = md5(uniqid()) . '.' . $attestationAssurance->guessExtension();
                    $attestationAssurance->move($this->parameterBag->get('kernel.project_dir') . '/upload/attestations/', $attestationAssuranceName);
                    $attestationAssurancePath = $this->parameterBag->get('kernel.project_dir') . '/upload/attestations/' . $attestationAssuranceName;                   
                    $teletravailForm->setCreatedAt(new DateTimeImmutable());
                    $teletravailForm->setAttestationAssurance($attestationAssurancePath);
                    $teletravailForm->setUser($user);
                    $teletravailForm->setState(StateEnum::PENDING_MANAGER_VALIDATION);
                    $teletravailForm->setFonctionExercee($user->getMetier());
                    $teletravailForm->setManager($user->getManager());
                    $entityManager->persist($teletravailForm);
                    $entityManager->flush();
                    $this->sendMailService->sendEmailToManager($teletravailForm, $this->urlGeneratorService->generate('app_teletravailform_manager_edit', ['id' => $teletravailForm->getId()]));
                    return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', 'Votre demande de télétravail régulier a bien été prise en compte.')], Response::HTTP_SEE_OTHER);
                }
            } else {
                return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', "Vous n'avez pas le droit au télétravail.")], Response::HTTP_SEE_OTHER);
            }

            return $this->render('collaborator/teletravail_form/new.html.twig', [
                'user' => $this->getUser(),
                'teletravail_form' => $teletravailForm,
                'form' => $form,
            ]);
    }

    #[Route('/{id}', name: 'app_teletravailform_show', methods: ['GET'])]
    public function show(Request $request, TeletravailForm $teletravailForm): Response
    {
        $user = $this->getUserService->getCurrentUser($this->getUser());

        $form = $this->createForm(TeletravailFormType::class, $teletravailForm, [
            'user_roles'  => $teletravailForm->getUser()->getRoles(),
            'request' => $request,
            'current_user' => $teletravailForm->getUser(),
        ]);

        return $this->render('collaborator/teletravail_form/show.html.twig', [
            'teletravail_form' => $teletravailForm,
            'form' => $form,
            'user' => $user,
        ]);
    }

    #[Route('/pdf/{id}/{entity}', name: 'app_download_pdf', methods: ['GET'])]
    public function downloadPdf(string $entity, EntityManagerInterface $em, int $id): Response
    {
        $entity = $entity === "teletravailform" ? "TeletravailForm" : ucfirst($entity);
        return $this->downloadPdfService->downloadPdf($em->getRepository("App\Entity\\".$entity)->findOneById($id), $entity);
    }

    #[Route('/{id}/edit', name: 'app_teletravailform_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TeletravailForm $teletravailForm, EntityManagerInterface $entityManager, $id, ParameterBagInterface $parameterBag, UserRepository $userRepository, ManagerRepository $managerRepository): Response
    {
        // Gérer l'accès à l'edit lors de la validation finale.
        $user = $this->getUser();
        if (($teletravailForm->getState() == StateEnum::VALITED_HR || $teletravailForm->getState() == StateEnum::REFUSED_HR) && $teletravailForm->getUser() == $user) {
            $form = $this->createForm(TeletravailFormType::class, $teletravailForm, [
                'user_roles'  => $user->getRoles(),
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $managerName = explode(' ', $teletravailForm->getManager()->getName());
                $html = $this->renderView('_pdf/teletravailform_pdf.html.twig', [
                    'teletravail_form' => $teletravailForm,
                    'user' => $user,
                    'signature_manager' =>   $managerName[0] .' '. ucfirst(strtolower($managerName[1])),
                    'drh' =>  $userRepository->findOneBy(['email' => $managerRepository->findOneBy(['departement' =>'DRH - Direction Ressources Humaines'])->getEmail()]),
                ]);
                $tmpTeletravailFormPdfPath = $this->createPdfService->create('teletravail_form_tmp', $html);
                $logoBase64 = base64_encode(file_get_contents($parameterBag->get('kernel.project_dir') . '/public/assets/images/hrdoc_logo.png'));
                $htmlAttestation = $this->renderView('_pdf/attestation_honneur_pdf.html.twig', [
                    'teletravail_form' => $teletravailForm,
                    'user' => $user,
                    'logo' => 'data:png;base64,' . $logoBase64,
                ]);
                $tmpAttestationPdfPath = $this->createPdfService->create('attestation_tmp', $htmlAttestation);
                $attestationAssurancePath =  $teletravailForm->getAttestationAssurance();
                $teletravailFormPdfPath = $this->parameterBag->get('kernel.project_dir') . '/upload/teletravailforms_pdf/' . $tmpTeletravailFormPdfPath[1];
                // Concatène les pdf en utilisant GhostScript, en premier fichier sur lequel les données vont être concaténées. Ensuite tous les fichiers qu'on souhaite concaténer.
                $command = 'gs -sDEVICE=pdfwrite -o ' . $teletravailFormPdfPath . ' ' . $tmpTeletravailFormPdfPath[0] . ' ' . $tmpAttestationPdfPath  .' ' .$attestationAssurancePath;
                shell_exec($command);
                // TODO : Faire une commande avec un cron
                unlink($tmpTeletravailFormPdfPath[0]);
                unlink($tmpAttestationPdfPath);
                if($teletravailForm->isAvisDRH()) {
                    $teletravailForm->setState(StateEnum::VALITED);
                } else {
                    $teletravailForm->setState(StateEnum::REFUSED);
                }
                $teletravailForm->setLocation($teletravailFormPdfPath);
                $entityManager->persist($teletravailForm);
                $entityManager->flush();
                $this->sendMailService->sendToCollaborator($teletravailForm, '', $teletravailFormPdfPath);
                return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', 'Votre procédure concernant votre demande de télétravail pour l\'année ' . $teletravailForm->getDateFinTeletravail()->format('Y') . ' est maintenant terminée.')], Response::HTTP_SEE_OTHER);
            }
            return $this->render('collaborator/teletravail_form/edit.html.twig', [
                'user' => $user,
                'teletravail_form' => $teletravailForm,
                'form' => $form,
            ]);
        }
        throw new AccessDeniedException('Vous n\'avez pas les droits pour modifier ce formulaire.');
    }

    #[Route('/{id}/edit/reopen', name: 'app_teletravailform_reopen', methods: ['GET', 'POST'])]
    public function reopen(Request $request, TeletravailForm $teletravailForm, EntityManagerInterface $entityManager): Response
    {
        // Renvoyer vers connection si non connecté.
        // Gérer l'accès à l'edit lors de la validation finale.
        $user = $this->getUserService->getCurrentUser($this->getUser());
        if ($teletravailForm->getState() == StateEnum::REOPEN && $teletravailForm->getUser() == $user) {
            $form = $this->createForm(TeletravailFormType::class, $teletravailForm, [
                'user_roles'  => $user->getRoles(),
                'teletravail_form_state' => $teletravailForm->getState(),
                'request' => $request,
                'id' => $teletravailForm->getId(),
            ]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $attestationAssurance = $form['attestationAssurance']->getData();
                $attestationAssuranceName = md5(uniqid()) . '.' . $attestationAssurance->guessExtension();
                $attestationAssurance->move($this->parameterBag->get('kernel.project_dir') . '/upload/attestations', $attestationAssuranceName);
                $attestationAssurancePath = $this->parameterBag->get('kernel.project_dir') . '/upload/attestations/' . $attestationAssuranceName;
                $teletravailForm->setAttestationAssurance($attestationAssurancePath);
                $teletravailForm->setState(StateEnum::PENDING_HR_VALIDATION);
                $entityManager->flush();
                $this->sendMailService->sendEmailToRh($teletravailForm,  $this->urlGeneratorService->generate('app_teletravail_form_rh_edit', ['id' => $teletravailForm->getId()]));
                return $this->redirectToRoute('app_dashboard', [$this->addFlash('success', 'Votre demande de télétravail régulier pour l\'année ' . date('Y') . ' a bien été modifiée.')], Response::HTTP_SEE_OTHER);
            }
            return $this->render('collaborator/teletravail_form/edit.html.twig', [
                'form' => $form,
                'user' => $user,
                'state' => $teletravailForm->getState(),
            ]);
        }
        throw new AccessDeniedException('Vous n\'avez pas les droits pour modifier ce formulaire.');
    }

}
