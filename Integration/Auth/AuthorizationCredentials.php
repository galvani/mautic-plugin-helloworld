<?php declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\Integration\Auth;

use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\CodeInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\CredentialsInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\RedirectUriInterface;

class AuthorizationCredentials implements CredentialsInterface, CodeInterface, RedirectUriInterface
{
    public function __construct(
        private ?string  $clientId,
        private ?string  $clientSecret,
        private string  $redirectUri,
        private string  $authorizationUrl,
        private string  $tokenUrl,
        private ?string $code = null,
    )
    {
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getAuthorizationUrl(): string
    {
        return $this->authorizationUrl;
    }

    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }
}