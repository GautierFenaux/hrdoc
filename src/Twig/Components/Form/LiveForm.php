<?php

namespace App\Twig\Components\Form;

use App\Service\FormService;
use App\Entity\TeletravailForm;
use App\Form\Rh\RhTeletravailFormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Collaborator\TeletravailFormType;
use App\Form\Manager\ManagerTeletravailFormType;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\PostHydrate;
use Symfony\UX\LiveComponent\Attribute\PreDehydrate;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class LiveForm extends AbstractController
{
    // public function __construct() {}

    public function __construct(
        //   private ParameterBagInterface $parameterBag,
        private RequestStack $request,
        private FormService $formService,
        // private EntityManagerInterface $entityManager
    ) {}

    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public null $initialFormData = null;

    public ?string $typeOfCollaborator = 'collaborator';

    protected function instantiateForm(): FormInterface
    {
        $form = $this->initialFormData;
        $request = $this->request->getCurrentRequest();
        // dd($request->getPathInfo());
        if(strpos($request->getPathInfo(), 'rh')) {
            return $this->createForm(RhTeletravailFormType::class, $form);
        }
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

    // #[PreDehydrate]
    // public function test() {
    //     $request = $this->request->getCurrentRequest();
    //     dd($request->getPathInfo());
    // }

    #[PostHydrate(priority: 1)]
    public function resetTypeOfCollaborator(): void
    {
        $this->typeOfCollaborator = "rh";
    }
}
