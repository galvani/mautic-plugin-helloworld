<?php declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\Integration\Auth;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use function PHPUnit\Framework\assertClassHasAttribute;
use function PHPUnit\Framework\assertInstanceOf;

/**
 * @property RouterInterface $router
 */
trait DefaultRedirectUriTrait
{
    public function getCallbackUrl(string $integrationName): string
    {
        assertClassHasAttribute('router', self::class);
        assertInstanceOf(RouterInterface::class, $this->router);


    }
}