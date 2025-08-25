<?php

namespace App\Twig\Components\Form;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class FormWrapper
{
    public string $class = "form-wrapper";
    public ?string $dataController = null;
}