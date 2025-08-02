<?php
namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class FormSubscriber implements EventSubscriberInterface
{

    private $currentUser;

    public function __construct(User $currentUser)
    {
        $this->currentUser = $currentUser ;
    }


    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
        ];
    }

    public function onPreSetData(FormEvent $event) : void
    {
        if ($this->currentUser->getEmail() != $event->getData()->getManager()->getEmail()) {
            throw new AccessDeniedException('Vous n\'avez pas les droits pour modifier ce formulaire.');
        }
    }
}