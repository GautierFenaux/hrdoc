<?php

namespace App\Twig\Components\Form;

use App\Service\FormService;
use App\Entity\TeletravailForm;
use App\Form\TeletravailFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\PostHydrate;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class LiveForm extends AbstractController
{
    // public function __construct() {}

    public function __construct(
        //   private ParameterBagInterface $parameterBag,
        private FormService $formService,
        // private EntityManagerInterface $entityManager
    ) {}

    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?TeletravailForm $initialFormData = null;

    public ?string $typeOfCollaborator = 'collaborator';

    protected function instantiateForm(): FormInterface
    {
        $form = $this->initialFormData;

        return $this->createForm(TeletravailFormType::class, $form);
    }

    #[LiveAction]
    public function save()
    {
        // Submit the form! If validation fails, an exception is thrown
        // and the component is automatically re-rendered with the errors
        $this->submitForm();

        $redirectingRoute = $this->formService->checkData($this->getForm()->getData(), $this->formValues);

        return $this->redirectToRoute($redirectingRoute[0], $redirectingRoute[1], $redirectingRoute[2]);
    }

    #[PostHydrate(priority: 1)]
    public function resetTypeOfCollaborator(): void
    {
        $this->typeOfCollaborator = "rh";
        $form = $this->initialFormData;
    }
}
