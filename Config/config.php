<?php

declare(strict_types=1);    // not really worth anything here, but good practice

return [
    'name' => 'HelloWorld',
    'description' => 'A sample Mautic plugin for your inspiration',
    'version' => '1.0',
    'authors' => [
        [
            'name' => 'Jan Kozak',
            'email' => 'galvani78@gmail.com',
            'role' => 'Developer',
        ],
    ],
    'services' => [
        'integrations' => [
            'mautic.integration.helloworld' => [
                'class' => \MauticPlugin\HelloWorldBundle\Integration\HelloWorldIntegration::class,
                'arguments' => [
                    'mautic.helloworld.configuration',
                    'request_stack',
                    'translator',
                ],
                'tags' => [ // @todo tagging should be refactored to use services.php
                    'mautic.integration',
                    'mautic.basic_integration',
                ],
            ],
            // Provides the form types to use for the configuration UI
            'mautic.integration.helloworld.form_config' => [
                'class' => \MauticPlugin\HelloWorldBundle\Integration\Support\ConfigSupport::class,
                'arguments' => [
                    'mautic.helloworld.configuration',
                    'request_stack',
                    'translator',
                ],
                'tags' => ['mautic.config_integration'], // @todo tagging should be refactored to use services.php
            ],
        ]
    ],
];
