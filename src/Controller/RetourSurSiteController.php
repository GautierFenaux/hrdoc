<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\RetourSurSite;
use App\Form\RetourSurSiteType;
use App\Service\GetUserService;
use App\Repository\UserRepository;
use App\Repository\ManagerRepository;
use App\Repository\TeletravailFormRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/retour-sur-site')]
class RetourSurSiteController extends AbstractController
{

    #[Route('/{id}/edit', name: 'app_retour_sur_site_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RetourSurSite $retourSurSite, EntityManagerInterface $entityManager, UserRepository $userRepository, ManagerRepository $managerRepository, ParameterBagInterface $parameterBag, TeletravailFormRepository $teletravailFormRepository ): Response
    {
        $user = $this->getUser();
        
        if (($retourSurSite->getState() !== 'validé-rh' || $retourSurSite->getState() !== 'refus-rh') && $retourSurSite->getUser() !== $user) {
            throw new AccessDeniedException('Vous n\'avez pas les droits pour modifier ce formulaire.');
        }


        $form = $this->createForm(RetourSurSiteType::class, $retourSurSite, ['validation' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $retourSurSite->setSignatureCollab(true);

            if ($retourSurSite->getState('refus-rh')) {
                $retourSurSite->setState('refus');
            } else {
                $retourSurSite->setState('validé');
            }

            // if($form->getData()->isEntretienRh()) {
            //     // Envoyer un mail si demande d'entretien RH?
            // }
            
            // trouve le dernier TeletravailForm
            $teletravailStartDate = $teletravailFormRepository->findOneBy(['user' => $this->getUserService->getCurrentUser($this->getUser())], [
                'id' => 'DESC'
            ])->getACompterDu();
            

            // Générer le pdf ici
            $rhSignatory = $userRepository->findOneBy(['email' => $managerRepository->findOneBy(['departement' => 'DRH - Direction Ressources Humaines'])->getEmail()]);
            $logoBase64 = base64_encode(file_get_contents($parameterBag->get('kernel.project_dir') . '/public/assets/images/hrdoc_logo.png'));

            $html = $this->renderView('_pdf/retour_sur_site_pdf.html.twig', [
                'signature_rh' => $rhSignatory->getName() . ' ' .  ucfirst(strtolower($rhSignatory->getSurname())),
                'rss' => $retourSurSite,
                'teletravail_start_date' => $teletravailStartDate,
                'logo' => 'data:png;base64,' . $logoBase64,
            ]);

            $tmp = sys_get_temp_dir();
            $dompdf = new Dompdf([
                'logOutputFile' => '',
                'isRemoteEnabled' => true,
                'fontDir' => $tmp,
                'fontCache' => $tmp,
                'tempDir' => $tmp,
                'chroot' => $tmp,
            ]);

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();

            $filename = md5(uniqid()) . '.pdf';
            

            $retourSurSitePdfPath = $parameterBag->get('kernel.project_dir') . '/upload/retour_sur_site/' . $filename;

            file_put_contents($retourSurSitePdfPath, $output);


            $retourSurSite->setLocation($retourSurSitePdfPath);

            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard',  [$this->addFlash('success', 'La demande de retour sur site a été validée')], Response::HTTP_SEE_OTHER);
        }

        return $this->render('retour_sur_site/edit.html.twig', [
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
}
