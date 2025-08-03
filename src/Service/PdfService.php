<?php

namespace App\Service;

use Dompdf\Dompdf;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class PdfService
{
    public function __construct(private ParameterBagInterface $parameterBag){}

    public function create(string $entity, string $html): array|string
    {
        $tmp = sys_get_temp_dir();
        $dompdf = new Dompdf([
            'logOutputFile' => '',
            'isRemoteEnabled' => true,
            'fontDir' => $tmp,
            'fontCache' => $tmp,
            'tempDir' => $tmp,
            'chroot' => $tmp,
            'defaultFont' => 'Arial'
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
        $filename = md5(uniqid()) . '.pdf';
        // Passer le chemin jusqu'au dossier en argument de la fonction
        $pdfPath = $this->parameterBag->get('kernel.project_dir') . '/upload/'.$entity.'/'.$filename;
        file_put_contents($pdfPath, $output);
        // TODO : Checker l'instance de l'entité, du coup passer un objet ?
        if($entity === 'teletravail_form_tmp' || $entity === 'teletravail_form') {
            return [$pdfPath, $filename];
        }
        return $pdfPath;
    }
      
    
}