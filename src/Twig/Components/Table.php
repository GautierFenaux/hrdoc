<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Table
{
    public array $head = [];
    public array $entities = [];
    public ?string $id = null;
    public string $class = "table-style cell-border compact stripe dataTable";
}