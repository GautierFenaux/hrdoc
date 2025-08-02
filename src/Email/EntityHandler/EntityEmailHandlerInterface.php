<?php

namespace App\Email\EntityHandler;

use App\Email\Dto\EmailData;

interface EntityEmailHandlerInterface
{
    public function supports(object $form): bool;

    public function getManagerEmailData(object $form, string $url): ?EmailData;
            
    public function getRhEmailData(object $form, string $url): ?EmailData;

    public function getCollaboratorEmailData(object $form, string $url, string|null $pathToPdf): ?EmailData;
}
