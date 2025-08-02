<?php

/**
 * This file is part of ORCC.
 */
declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use App\Exception\AuthenticationGroupException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class LoginEventListener {
    /**
     * LoginEventListener constructor.
     */
    public function __construct(private EntityManagerInterface $em,private TokenStorageInterface $tokenStorage, private UserRepository $userRepository) {
    }

    public function onLoginSuccess(InteractiveLoginEvent $event): void {
        
        // $request = $event->getRequest();
        // $token = $event->getAuthenticationToken();
        // $loggedUser = $token->getUser();
        // $session = $request->getSession();

        // if (!($loggedUser instanceof User)) {
   
        //     $user = $this->userRepository->findOneBy(['email' =>]);
        //     if($user === NULL) {
        //         throw new AccessDeniedException();
        //         return;
        //     }

        //     if(true === $user->isFirstConnection()) {
        //         $user->setFirstConnection(false);
        //         $this->em->persist($user);
        //     }

        //     if(null === $user->isFirstConnection()) {
        //         $user->setFirstConnection(true);
        //         $this->em->persist($user);
        //     }
     
        //     $this->em->flush();
        //     $session->set('user', $user);
        // }
    }
}

