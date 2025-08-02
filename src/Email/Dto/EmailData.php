<?php

namespace App\Email\Dto;

// Permet d'être réutilisé dans les différents Handler pour typer le retour de chaque méthode
class EmailData
{
    public function __construct(
        public string $to,
        public string $subject,
        public string $template,
        public array $context = [],
        public ?string $cc = null,
        public ?string $attachment = null,
    ) {}
}
