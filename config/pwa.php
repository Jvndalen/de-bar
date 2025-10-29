<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Would you like the install button to appear on all pages?
      Set true/false
    |--------------------------------------------------------------------------
    */

    'install-button' => true,

    /*
    |--------------------------------------------------------------------------
    | PWA Manifest Configuration
    |--------------------------------------------------------------------------
    |  php artisan erag=>update-manifest
    */

    'manifest' => [
        'name' => 'De Bar',
        'short_name' => 'De Bar',
        'background_color' => '#000032',
        'display' => 'standalone',
        'description' => 'De bar web applicatie voor waterscouting De IJssel',
        'theme_color' => '#3A3A3A',
        'icons' => [
            [
                'src' => 'logo.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ],
            [
                "src"=> "logo.png",
                "sizes"=> "512x512",
                "type" => "image/png",
                "purpose"=> "maskable"
            ],
            [
                "src"=> "logo.png",
                "sizes"=> "512x512",
                "type" => "image/png",
                "purpose"=> "any"
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Configuration
    |--------------------------------------------------------------------------
    | Toggles the application's debug mode based on the environment variable
    */

    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Livewire Integration
    |--------------------------------------------------------------------------
    | Set to true if you're using Livewire in your application to enable
    | Livewire-specific PWA optimizations or features.
    */

    'livewire-app' => false,
];
