<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlGeneratorService
{


    public function __construct(private RequestStack $requestStack, private UrlGeneratorInterface $urlGeneratorInterface){}

    public function generate(string $routeName, ?array $params = []): string
    {
        
        return 'http://' . $this->requestStack->getCurrentRequest()->getHost() . 
            $this->urlGeneratorInterface->generate($routeName, 
            $params, UrlGeneratorInterface::ABSOLUTE_PATH);
    }
      
    
}