<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[AsTwigComponent]
class Link
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {}
    public ?string $classToAdd = null;
    public ?string $case = null;
    public string $action;
    public string $route;
    public object|array|null $entity = null;
    public ?string $role = null;
    public bool $isDisabled = false;
    public bool $isTargetBlank = false;
    // Permet d'appeler une fonction sur un stimulus controller via l'attribut data-action
    public ?string $dataAction = null;
    // Permet de définir si il s'agit d'un refus, d'une réouverture ou autre... Pour définir la logique dans les controller (ex: CetManagerController sur la route d'edit)
    // Permet également d'afficher la bonne icône (ref macro : templates/components/Link.html.twig)
    public ?string $dataRequestType = null;
    // Si on passe un array pour l'entité, typeOfEntity permet de spécifier pour quel type d'entité (cet, teletravail...) les données de l'array concernent
    public ?string $typeOfEntity = null;
    public ?array $params = null;
    public function url(): ?string
    {

        if (gettype($this->entity) !== 'array' && $this->entity !== null) {
            $class = strtolower((new \ReflectionClass($this->entity))->getShortName());
            $params = ['id' => $this->entity->getId()];
            if ($this->case) {
                $params['case'] = $this->case;
            }
        } else {
            $params = ['id' => $this->params['id'][0], 'status' => $this->params['status'][0]];
            $class = $this->typeOfEntity;
        }

        if ($this->action === 'download') {
            return $this->urlGenerator->generate('app_download_pdf', [
                'id' => $this->entity->getId(),
                'entity' => $class,
            ]);
        }

        $role = $this->role;
        if ($role) {
            return $this->urlGenerator->generate('app_' . $class . '_' . $role . '_' . $this->action, $params);
        }
        return $this->urlGenerator->generate('app_' . $class . '_' . $this->action, $params);
    }
}
