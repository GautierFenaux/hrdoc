<?php

namespace App\Service;

use Symfony\Component\Mime\Part\File;
use App\Email\Dto\EmailData;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use App\Email\EntityHandler\EntityEmailHandlerInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class SendMailService
{
    /**
     * @param iterable<EntityEmailHandlerInterface> $handlers
     */
    public function __construct(
        #[TaggedIterator('app.entity_email_handler')]
        private iterable $handlers,
        private MailerInterface $mailer,
    ) {}

    // Retourne le mail handler pour l'entité correspondante (CET, TeletravailForm, Astreinte)
    private function findHandler(object $form): ?EntityEmailHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($form)) {
                return $handler;
            }
        }
        return null;
    }


    private function setAndSendEmail(EmailData $data): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply-Hrdoc@hrdoc.fr')
            ->to($data->to)
            ->subject($data->subject)
            ->context($data->context)
            ->htmlTemplate($data->template);

        if ($data->cc) {
            $email->cc($data->cc);
        }

        if ($data->attachment) {
            $email->addPart(new DataPart(new File($data->attachment)));
        }

        try {
            $this->mailer->send($email);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }

    public function sendEmailToManager(object $form, string $url): void
    {
        $handler = $this->findHandler($form);
        if (!$handler) return;
        $data = $handler->getManagerEmailData($form, $url);

        if ($data) {
            $this->setAndSendEmail($data);
        }
    }

    public function sendEmailToRh(object $form, string $url): void
    {
        $handler = $this->findHandler($form);
        if (!$handler) return;
        $data = $handler->getRhEmailData($form, $url);

        if ($data) {
            $this->setAndSendEmail($data);
        }
    }

    public function sendToCollaborator(object $form, string $url, ?string $pathToPdf = null): void
    {
        $handler = $this->findHandler($form);
        if (!$handler) return;
        $data = $handler->getCollaboratorEmailData($form, $url, $pathToPdf);
        if ($data) {
            $this->setAndSendEmail($data);
        }
    }
}
