<?php


namespace App\Controller\Manager;

use DateTime;
use App\Entity\User;
use App\Enum\StateEnum;
use App\Entity\TeletravailForm;
use App\Service\SendMailService;
use App\Form\TeletravailFormType;
use App\Repository\UserRepository;
use App\Service\UrlGeneratorService;
use App\Repository\ManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TeletravailFormRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/manager/teletravailform')]
class TeletravailFormManagerController extends AbstractController
{


    public function __construct(private RequestStack $requestStack, private SendMailService $sendMailService, private UrlGeneratorService $urlGeneratorService)
    {
    }
    
    #[Route('/', name: 'app_teletravail_form_manager_index', methods: ['GET'])]
    public function index(TeletravailFormRepository $teletravailFormRepository, ManagerRepository $managerRepository): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        // Récupérer sur la table manager et afficher les formulaires lui ayant été soumis
        $userEmail =  $this->getUser()->getEmail();
        $manager = $managerRepository->findOneBy(['email' => $userEmail]);
        return $this->render('manager/teletravail_form/index.html.twig', [
            'teletravail_forms' => $teletravailFormRepository->findBy(['manager' => $manager]),
        ]);
    }


    #[Route('/{id}/edit', name: 'app_teletravailform_manager_edit', methods: ['GET', 'POST'])]
    public function managerEdit(Request $request, TeletravailForm $teletravailForm, EntityManagerInterface $entityManager, UserInterface $user, int $id): Response
    {
        $loggedUser = $this->getUser();
        if (!$loggedUser instanceof User || !in_array('ROLE_MANAGER', $loggedUser->getRoles())) {
            throw $this->createAccessDeniedException();
        }
        // Gérer l'accès à l'edit lors de la validation finale utiliser un role_edit.
        // Permet de passer l'objet request dans les options du formulaire pour gérer l'affichage des champs en fonction des routes.
        $user =  $this->getUser();

        $form = $this->createForm(TeletravailFormType::class, $teletravailForm, [
            'user_roles'  => $user->getRoles(),
            'request' => $request,
            'current_user' => $user,
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($form['avisManager']->getData() == true) {
                $teletravailForm->setState(StateEnum::VALITED_MANAGER);
            } else {
                $teletravailForm->setState(StateEnum::REFUSED_MANAGER);
            }
            $entityManager->flush();
            $teletravailForm->setReceptionDemande(new DateTime());    
            $this->sendMailService->sendEmailToRh($teletravailForm, $this->urlGeneratorService->generate('app_teletravailform_rh_edit', ['id' => $teletravailForm->getId()]));
            $this->addFlash('success', 'Votre validation pour la demande de ' .  $teletravailForm->getUser()->getName() . ' ' . $teletravailForm->getUser()->getsurName() . ' a bien été prise en compte.');
            return $this->redirectToRoute('app_teletravail_form_manager_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('manager/teletravail_form/edit.html.twig', [
            'teletravail_form' => $teletravailForm,
            'form' => $form,
        ]);
    }


}
