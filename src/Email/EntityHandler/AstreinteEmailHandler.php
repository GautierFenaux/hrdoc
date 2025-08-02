<?php

namespace App\Email\EntityHandler;

use App\Entity\Astreinte;
use App\Email\Dto\EmailData;

class AstreinteEmailHandler implements EntityEmailHandlerInterface
{
    public function supports(object $entity): bool
    {
        return $entity instanceof Astreinte;
    }

    public function getManagerEmailData(object $entity, string $url): EmailData
    {
        // On recherche l'état de l'entité pour afficher le bon titre de mail
        $subject = match ($entity->getState()) {
            'refus-collaborateur' => 'Refus de la demande d\'heures supplémentaire',
            'validé-collaborateur-postop' => 'Saisie des temps post opération à valider',
            'refus-collaborateur-postop' => 'Demande d\'opérations refusée',
            default => 'Demande d\'opérations à valider',
        };

        return new EmailData(
            $entity->getUser()->getEmail(),
            $subject,
            'emails/astreinte/astreinte_to_manager.html.twig',
            [
                'form' => $entity,
                'target_url' => $url,
            ]
        );
    }

    public function getRhEmailData(object $entity, string $url): EmailData
    {
        $collaboratorName = $entity->getUser()->getName() . ' ' . $entity->getUser()->getSurname();

        return new EmailData(
            'drh@hrdoc.fr',
            'Demande d\'opérations concernant ' . $collaboratorName,
            'emails/astreinte/astreinte_to_rh.html.twig',
            ['form' => $entity, 'target_url' => $url],
        );
    }

    public function getCollaboratorEmailData(object $entity, string $url, string|null $pathToPdf): EmailData
    {
        $state = $entity->getState();
        $subject = 'Demande d\'opération';
        $template = 'emails/astreinte/astreinte_to_collaborator.html.twig';
        $copyTo = null;

        [$subject, $template, $copyTo] = match ($state) {
            'validé-rh' => ['Demande d\'opération validée', $template, $entity->getManager()->getEmail()],
            'refus-rh' => ['Demande d\'opération refusée', $template, $entity->getManager()->getEmail()],
            'validé-rh-postop' => ['Validation des temps saisies post opération', 'emails/astreinte/astreinte_to_collaborator_postop.html.twig', null],
            'réouvert-rh-postop' => ['Réouverture des temps saisies post opération', 'emails/astreinte/astreinte_to_collaborator_postop.html.twig', null],
            default => [$subject, $template, null],
        };

        return new EmailData(
            $entity->getUser()->getEmail(),
            $subject,
            $template,
            ['form' => $entity, 'target_url' => $url],
            $copyTo,
            $pathToPdf,
        );
    }
}
