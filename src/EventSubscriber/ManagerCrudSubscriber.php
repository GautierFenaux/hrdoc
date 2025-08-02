<?php

namespace App\EventSubscriber;

use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class ManagerCrudSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserRepository $userRepository){
        
    }

    private function getUser(string $email) {
        return $this->userRepository->findOneByEmail($email);
    }

     
    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $user = $this->getUser($event->getEntityInstance()->getEmail());
        $roles = $user->getRoles();
        array_push($roles, 'ROLE_MANAGER');
        $user->setRoles($roles);
    }
    // Permet d'enlever le role manager sur le user au moment du delete du manager
    public function onBeforeEntityDeletedEvent(BeforeEntityDeletedEvent $event) {
        if(isset($user)) {
            $user = $this->getUser($event->getEntityInstance()->getEmail());
            $roles = $user->getRoles();
            unset($roles[array_search('ROLE_MANAGER', $roles)]);
            $user->setRoles($roles);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityDeletedEvent::class=> 'onBeforeEntityDeletedEvent',
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistedEvent',
        ];
    }
}
