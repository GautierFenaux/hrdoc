<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;

class GetUserService
{


    public function __construct(private UserRepository $userRepository, private User $user)
    {
    }

    public function getCurrentUser($currentUser) {
        $this->user = $this->userRepository->findOneBy(['email' => $currentUser->getEmail()]);
        return $this->user ;
    }
      
    
}