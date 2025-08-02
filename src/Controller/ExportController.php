<?php

namespace App\Controller;


use App\Service\ExportService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ExportController extends AbstractController
{
    

    public function __construct(private ExportService $exportService)
    {
    }

    // #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour accéder à cette page.')]
    #[Route('/export-formulaire/{type}', name: 'app_export_formulaire')]
    public function exportTeletravailForm(string $type)
    {
        try {
            $csvPath = $this->exportService->exportData($type);
            $response = new BinaryFileResponse($csvPath[0]);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $csvPath[1],
            );
            return $response;
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            return new Response($errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
