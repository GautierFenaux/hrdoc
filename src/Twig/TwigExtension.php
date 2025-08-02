<?php

namespace App\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use DateTime;

// Permet de créer des test sur les templates twig, utilisé dans le twig component. 
// Utile pour le composant Link pour créer des link dynamique en fonction de l'entité
class TwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('get_class_name', [$this, 'getEntityName']),
            new TwigFunction('format_date', [$this, 'formatDate']),
        ];
    }
    // Check le type d'entité (cet, teletravailForm, astreinte...)
    public function getEntityName($object): string
    {
        return is_object($object) ? strtolower((new \ReflectionClass($object))->getShortName()) : strtolower(gettype($object));
    }

    public function formatDate($dateString)
    {
        $date = new DateTime($dateString);
        // Crée un formateur pour la date longue en français
        $formatter = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::FULL, // format de date
            \IntlDateFormatter::NONE, // format de l'heure
            $date->getTimezone(),
            \IntlDateFormatter::GREGORIAN
        );

        echo $formatter->format($date);
    }
}
