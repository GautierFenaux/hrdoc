<?php

namespace App\Email\EntityHandler;

use App\Email\Dto\EmailData;
use App\Entity\TeletravailForm;
use App\Service\UrlGeneratorService;
use Symfony\Component\HttpFoundation\RequestStack;

class TeletravailFormEmailHandler implements EntityEmailHandlerInterface
{
    public function __construct(
        private UrlGeneratorService $urlGeneratorService,
        private RequestStack $requestStack,
    ) {}

    public function supports(object $entity): bool
    {
        return $entity instanceof TeletravailForm;
    }

    public function getManagerEmailData(object $entity, string $url): EmailData
    {
        /** @var TeletravailForm $entity */
        $user = $entity->getUser();
        $manager = $user->getManager();
        $url = $this->urlGeneratorService->generate('app_teletravailform_manager_edit', ['id' => $entity->getId()]);

        return new EmailData(
            $manager->getEmail(),
            'Demande de télétravail à valider par le manager',
            'emails/teletravail/teletravail_to_manager.html.twig',
            [
                'target_url' => $url,
                'form' => $entity,
            ],
        );
    }

    public function getRhEmailData(object $entity, string $url): EmailData
    {
        /** @var TeletravailForm $entity */
        $collaborator = $entity->getUser();
        $collaboratorName = $collaborator->getName() . ' ' . $collaborator->getSurname();
        $url = $this->urlGeneratorService->generate('app_teletravailform_rh_index', ['id' => $entity->getId()]);

        $state = $entity->getState();
        $template = '';
        $subject = '';

        if ($state === 'validé-manager' || $state === 'refus-manager') {
            $template = 'emails/teletravail/teletravail_to_rh.html.twig';
            $subject = 'Demande de télétravail à valider par la direction des ressources humaines';
        } else {
            $template = 'emails/teletravail/teletravail_reopen_to_rh.html.twig';
            $subject = 'Mise à jour de la demande de télétravail de ' . $collaboratorName;
        }

        return new EmailData(
            'teletravail@hrdoc.fr',
            $subject,
            $template,
            [
                'target_url' => $url,
                'form' => $entity,
            ],
        );
    }

    public function getCollaboratorEmailData(object $entity, string $url, string|null $pdfPath): EmailData
    {
        /** @var TeletravailForm $entity */
        $collaborator = $entity->getUser();

        $state = $entity->getState();
        $subject = '';
        $template = '';

        if ($state === 'réouvert') {
            $subject = 'Formulaire de télétravail : modifications à apporter';
            $template = 'emails/teletravail/teletravail_reopen.html.twig';
        } elseif ($state === 'validé' || $state === 'refusé') {
            $subject = 'Formulaire de télétravail : validation finale';
            $template = 'emails/teletravail/teletravail_final.html.twig';
        } else {
            $subject = 'Validation définitive de votre demande de télétravail';
            $template = 'emails/teletravail/teletravail_final.html.twig';
        }

        return new EmailData(
            $collaborator->getEmail(),
            $subject,
            $template,
            [
                'target_url' => $url,
                'form' => $entity,
            ],
            null,
            $pdfPath,
        );
    }
}
