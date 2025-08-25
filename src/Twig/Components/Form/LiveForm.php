<?php

namespace App\Twig\Components\Form;

use App\Entity\TeletravailForm;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class LiveForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?TeletravailForm $initialFormData = null;

    protected function instantiateForm(): FormInterface
    {
        $form = $this->initialFormData ?? new TeletravailForm();

        return $this->createForm(TeletravailForm::class, $form);
    }

}