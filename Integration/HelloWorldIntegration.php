<?php

declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\Integration;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use kamermans\OAuth2\Exception\AccessTokenRequestException;
use Mautic\IntegrationsBundle\Auth\Support\Oauth2\ConfigAccess\ConfigTokenPersistenceInterface;
use Mautic\IntegrationsBundle\Integration\BC\BcIntegrationSettingsTrait;
use Mautic\IntegrationsBundle\Integration\ConfigurationTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\IntegrationInterface;
use Mautic\PluginBundle\Exception\ApiErrorException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class HelloWorldIntegration implements IntegrationInterface
{
    use BcIntegrationSettingsTrait;
    use ConfigurationTrait;

    public const NAME = 'HelloWorld';
    public const DISPLAY_NAME = 'Hello World Bundle';

    public function __construct(
        protected HelloWorldConfiguration $configuration,
        protected RequestStack            $requestStack,
        protected TranslatorInterface     $translator,
    )
    {
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/HelloWorldBundle/Assets/img/plugin_logo.png';
    }

    public function authCallback()
    {
        $client = $this->getAuthorizedClient();
        try {
            $client->get('https://api.getgo.com/G2M/rest/historicalMeetings', [
                'query' => [
                    'startDate' => (new \DateTimeImmutable('2020-01-01'))->format('c'),
                    'endDate' => (new \DateTimeImmutable('2020-01-01'))->format('c'),
                ],
            ]);
        } catch (AccessTokenRequestException $exception) {  // TODO do
            return $exception->getMessage();
        } catch (ClientException $exception) {
            return $exception->getMessage();
        }

        return false;   // means no error
    }

    protected function getAuthorizedClient(): ClientInterface
    {
        if ($this->requestStack->getSession() && MAUTIC_ENV !== 'dev') {
            $state = $this->requestStack->getSession()->get($this->getName() . '_csrf_token', false);

            if ($state !== $this->requestStack->getCurrentRequest()->get('state')) {
                $this->requestStack->getSession()->remove($this->getName() . '_csrf_token');
                throw new ApiErrorException('mautic.integration.auth.invalid.state'); // TODO check translation
            }
        }

        $request = $this->requestStack->getCurrentRequest();

        //$credentials = $this->configuration->getAuthorizationCredentials();

        $credentials = $this->configuration->getCredentials();
        $credentials->setCode($request->get('code'));
        $credentials->setState($request->get('state'));

        //  remove oauth token and refresh token
        $credentials->setAccessToken(null)->setRefreshToken(null);

        //  this call will perform token request and save token to credentials
        $client = $this->configuration->getHttpClient($credentials);

        return $client;
    }

    public function getUserData($identifier, &$socialCache) {
        return [];   // TODO Perhaps we can get some data from the API but see no reason nor endpoint to do so
    }
}

