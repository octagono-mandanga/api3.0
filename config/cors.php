<?php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:4200',        // Desarrollo local de Angular
        'https://mandanga.co',           // Tu dominio principal
        'https://*.mandanga.co',         // Subdominios de colegios
        'https://colegio1.edu.co',       // Dominios externos de colegios
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
