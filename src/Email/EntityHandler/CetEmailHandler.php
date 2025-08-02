<?php


namespace App\Email\EntityHandler;

use App\Entity\Cet;
use App\Email\Dto\EmailData;
use App\Enum\StateEnum;

class CetEmailHandler implements EntityEmailHandlerInterface
{
    public function supports(object $form): bool
    {
        return $form instanceof Cet;
    }

    public function getManagerEmailData(object $form, string $url): EmailData
    {
        $collaboratorName = $form->getUser()->getName() . ' ' . $form->getUser()->getSurname();

        return new EmailData(
            $form->getUser()->getEmail(),
            'Demande relative au compte épargne temps de ' . $collaboratorName,
            'emails/cet/cet_email.html.twig',
            ['form' => $form, 'target_url' => $url],
        );
    }

    public function getRhEmailData(object $form, string $url): EmailData
    {
        $collaboratorName = $form->getUser()->getName() . ' ' . $form->getUser()->getSurname();
        $subject = $form->getState() === StateEnum::REFUSED_MANAGER
            ? 'Demande de CET de ' . $collaboratorName . ' refusée'
            : ($form->isRestitution() ? 'Demande de restitution de CET' : 'Demande ' . ($form->isAlimentation() ? 'd\'alimentation de CET' : 'd\'utilisation de CET') . ' de ' . $collaboratorName);

        return new EmailData(
            'cet@hrdoc.fr',
            $subject,
            'emails/cet/cet_email_to_rh.html.twig',
            ['form' => $form, 'target_url' => $url],
       );
    }

    public function getCollaboratorEmailData(object $form, string $url, string|null $pathToPdf): EmailData
    {
        $collaboratorName = $form->getUser()->getName() . ' ' . $form->getUser()->getSurname();

        $subject = match($form->getState()) {
            StateEnum::REOPEN => 'Réouverture de la demande CET de ' . $collaboratorName,
            StateEnum::REFUSED_HR => 'Demande de CET de ' . $collaboratorName . ' refusée.',
            default => 'Demande de CET validée'
        };
        return new EmailData(
            $form->getUser()->getEmail(),
            $subject,
            'emails/cet/cet_email_from_rh.html.twig',
            ['form' => $form, 'target_url' => $url],
            null,
            $pathToPdf,
        );
    }
}
