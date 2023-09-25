<?php

declare(strict_types=1);

use Mautic\CoreBundle\DependencyInjection\MauticCoreExtension;
use MauticPlugin\HelloWorldBundle\Integration\HelloWorldConfiguration;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $excludes = ['Integration/Auth'];

    $services->load('MauticPlugin\\HelloWorldBundle\\', '../')
        ->exclude('../{'.implode(',', array_merge(MauticCoreExtension::DEFAULT_EXCLUDES, $excludes)).'}');

    $services->alias('mautic.helloworld.configuration', HelloWorldConfiguration::class);
};
