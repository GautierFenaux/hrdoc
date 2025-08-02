<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'jquery' => [
        'version' => '3.7.1',
    ],
    'datatables' => [
        'version' => '1.10.18',
    ],
    'datatables.net' => [
        'version' => '2.3.1',
    ],
    'datatables.net-dt' => [
        'version' => '2.3.1',
    ],
    'datatables.net-dt/css/dataTables.dataTables.min.css' => [
        'version' => '2.3.1',
        'type' => 'css',
    ],
    'flatpickr' => [
        'version' => '4.6.13',
    ],
    'flatpickr/dist/flatpickr.min.css' => [
        'version' => '4.6.13',
        'type' => 'css',
    ],
    'flatpickr/dist/l10n/fr.js' => [
        'version' => '4.6.13',
    ],
    'chart.js' => [
        'version' => '3.9.1',
    ],
    '@fortawesome/fontawesome-free' => [
        'version' => '6.7.2',
    ],
    '@fortawesome/fontawesome-free/css/fontawesome.min.css' => [
        'version' => '6.7.2',
        'type' => 'css',
    ],
    '@fortawesome/fontawesome-free/css/all.min.css' => [
        'version' => '6.7.2',
        'type' => 'css',
    ],
    'toastify-js' => [
        'version' => '1.12.0',
    ],
    'toastify-js/src/toastify.min.css' => [
        'version' => '1.12.0',
        'type' => 'css'
    ],
];
