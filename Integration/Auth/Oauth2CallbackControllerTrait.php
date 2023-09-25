<?php declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\Integration\Auth;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use kamermans\OAuth2\Exception\AccessTokenRequestException;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\CredentialsInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\HttpFactory;
use Mautic\IntegrationsBundle\Auth\Support\Oauth2\ConfigAccess\ConfigTokenPersistenceInterface;
use Mautic\PluginBundle\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait Oauth2CallbackControllerTrait
{

}