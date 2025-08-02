<?php

namespace App\Twig\Components\TableExt;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class DashboardTable
{
    public object $entity;
}