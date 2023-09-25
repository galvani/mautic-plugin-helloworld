<?php

declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\Integration;

use kamermans\OAuth2\Persistence\ClosureTokenPersistence;
use kamermans\OAuth2\Persistence\TokenPersistenceInterface as KamermansTokenPersistenceInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\CredentialsInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\HttpFactory;
use Mautic\IntegrationsBundle\Auth\Support\Oauth2\ConfigAccess\ConfigTokenPersistenceInterface;
use Mautic\IntegrationsBundle\Helper\IntegrationsHelper;
use Mautic\PluginBundle\Entity\Integration;
use MauticPlugin\HelloWorldBundle\Integration\Auth\OAuth2ThreeLeggedCredentials;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/** @todo this function really sux */
class HelloWorldConfiguration implements ConfigTokenPersistenceInterface
{
    public function __construct(
        private IntegrationsHelper $helper,
        private RouterInterface    $router,
        private RequestStack       $requestStack,
        private HttpFactory        $httpFactory,
    )
    {
    }

    public function getHttpFactory(): HttpFactory
    {
        return $this->httpFactory;
    }

    public function getHttpClient(?CredentialsInterface $credentials = null)
    {
        //  We will use this class as token persistence for the http client but any can be used, and perhaps it
        //  might be placed in a separate class or trait
        return $this->httpFactory->getClient($credentials ?? $this->getCredentials(), $this);
    }

    public function isAuthorized(): bool
    {
        $entity = $this->getIntegrationEntity();

        return $entity->getIsPublished()
            && ($entity->getApiKeys()['site_key'] ?? null) !== null
            && ($entity->getApiKeys()['secret_key'] ?? null) !== null
            && ($entity->getApiKeys()['access_token'] ?? null) !== null
            && ($entity->getApiKeys()['refresh_token'] ?? null) !== null
            && ($entity->getApiKeys()['expires_at'] ?? null) !== null;
    }

    public function getIntegrationEntity(): Integration
    {
        return $this->helper->getIntegration(HelloWorldIntegration::NAME)->getIntegrationConfiguration();
    }

    public function getCredentials(): OAuth2ThreeLeggedCredentials
    {
        $apiKeys = $this->getIntegrationEntity()->getApiKeys();

        return new OAuth2ThreeLeggedCredentials(
            $apiKeys['client_id'] ?? null,
            $apiKeys['client_secret'] ?? null,
            $apiKeys['access_token'] ?? null,
            $apiKeys['refresh_token'] ?? null,
            $this->getTokenUrl(),
            $this->getApiUrl(),
            $apiKeys['code'] ?? null,
            $apiKeys['state'] ?? null,
            $this->router->generate('mautic_integration_auth_callback',
                ['integration' => HelloWorldIntegration::NAME],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        );
    }

    public function getApiUrl(): string
    {
        return 'https://authentication.logmeininc.com';
    }

    public function getAuthorizationUrl(string $clientId = null): string
    {
        $apiKeys = $this->getIntegrationEntity()->getApiKeys();

        $state = $this->getAuthLoginState();
        $url = $this->getApiUrl() . '/oauth/authorize'
            . '?client_id=' . $apiKeys['client_id']
            . '&response_type=code'
            . '&redirect_uri=' . urlencode($this->getCallbackUrl())
            . '&state=' . $state;

        if ($this->getAuthScope()) {
            $url .= '&scope=' . urlencode($this->getAuthScope());
        }

        if ($this->requestStack->getSession()) {
            $this->requestStack->getSession()->set(HelloWorldIntegration::NAME . '_csrf_token', $state);
        }

        return $url;
    }

    public function getTokenUrl(): string
    {
        return $this->getApiUrl() . '/oauth/token';
    }

    public function getCallbackUrl(): string
    {
        return $this->router->generate(
            'mautic_integration_auth_callback',
            ['integration' => HelloWorldIntegration::NAME],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    public function getAuthLoginState(): string
    {
        return hash('sha1', uniqid((string)mt_rand()));
    }

    public function getAuthScope(): string // TODO probably not needed
    {
        return '';
    }

    public function getTokenPersistence(): KamermansTokenPersistenceInterface
    {
        return new ClosureTokenPersistence(
            function (array $keys) {    // Save tokens
                $standingKeys = $this->getIntegrationEntity()->getApiKeys();
                $standingKeys['access_token'] = $keys['access_token'] ?? null;
                $standingKeys['refresh_token'] = $keys['refresh_token'] ?? null;
                $standingKeys['expires_at'] = $keys['expires_ar'] ?? null;
                $configuration = $this->getIntegrationEntity();
                $configuration->setApiKeys($standingKeys);
                $this->helper->saveIntegrationConfiguration($configuration);
            },
            function (): ?array { // Restore tokens
                $keys = $this->getIntegrationEntity()->getApiKeys();
                if ($keys['access_token'] ?? null !== null) {
                    return [
                        'access_token' => $keys['access_token'] ?? null,
                        'refresh_token' => $keys['refresh_token'] ?? null,
                        'expires_at' => $keys['expires_at'] ?? null,
                    ];
                }
                return null;
            },
            function (): bool { //  Delete tokens
                $standingKeys = $this->getIntegrationEntity()->getApiKeys();
                unset($standingKeys['access_token']);
                unset($standingKeys['refresh_token']);
                unset($standingKeys['expires_at']);
                $configuration = $this->getIntegrationEntity();
                $configuration->setApiKeys($standingKeys);
                $this->helper->saveIntegrationConfiguration($configuration);

                return true;
            },
            function (): bool {
                $keys = $this->getIntegrationEntity()->getApiKeys() ?? null;
                return  $keys['access_token'] ?? null !== null;
            }
        );
    }
}
