<?php

namespace App\Controller;

use App\Service\GetUserService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class ProfilController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private GetUserService $getUserService
    ) {}

    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $user = $this->getUserService->getCurrentUser($this->getUser());
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'user' => $user,
        ]);
    }
}
