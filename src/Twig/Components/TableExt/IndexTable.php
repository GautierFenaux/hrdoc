<?php

namespace App\Twig\Components\TableExt;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class IndexTable
{
    public object $entity;
    public ?string $role = null;
    public ?string $dataMessage = null;
}