<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DownloadPdfService
{


    public function __construct(private EntityManagerInterface $entityManager){}

    public function downloadPdf($document, string $type): Response
    {
        $file = $document->getLocation();
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'demande_'.$type.'_de_'.strtolower($document->getUser()->getSurname()).'_'.strtoLower($document->getUser()->getName()).'.pdf'
        );
        return $response;
    }
      
    
}