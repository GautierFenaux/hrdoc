<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Accordion
{
    public ?string $class = "accordion";
    public array $entities = [];
}