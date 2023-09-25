<?php

namespace MauticPlugin\HelloWorldBundle\Integration\Auth;

use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\AccessTokenInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\CodeInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\CredentialsInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\RedirectUriInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2ThreeLegged\Credentials\RefreshTokenInterface;
use Mautic\IntegrationsBundle\Auth\Provider\Oauth2TwoLegged\Credentials\StateInterface;

class OAuth2ThreeLeggedCredentials
    implements AccessTokenInterface, CodeInterface, StateInterface, CredentialsInterface, RefreshTokenInterface, RedirectUriInterface
{
    // TODO consider expires_at but it's not used in the plugin as the TokenPersistance is handling it anyway
    public function __construct(
        private ?string $clientId = null,
        private ?string $clientSecret = null,
        private ?string $accessToken = null,
        private ?string $refreshToken = null,
        private ?string $tokenUrl = null,
        private ?string $baseUri = null,
        private ?string $code = null,
        private ?string $state = null,
        private ?string $redirectUri = null,
    ) {}

    public function getRedirectUri() : string {
        return $this->redirectUri;
    }

    public function getAuthorizationUrl(): string   // Check how to serve it considering the baseUri
    {
        return $this->baseUri ?? '' . '/oauth2/authorize';
    }

    public function setAccessToken(?string $accessToken): OAuth2ThreeLeggedCredentials
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function setRefreshToken(?string $refreshToken): OAuth2ThreeLeggedCredentials
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function setCode(?string $code): OAuth2ThreeLeggedCredentials
    {
        $this->code = $code;
        return $this;
    }

    public function setState(?string $state): OAuth2ThreeLeggedCredentials
    {
        $this->state = $state;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }


    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    // This method is never used, oauth middleware takes care of it
    public function getAccessTokenExpiry(): ?\DateTimeImmutable
    {
        return null;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getTokenUrl(): string
    {
        return $this->tokenUrl;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getBaseUri(): ?string
    {
        return $this->baseUri;
    }

    public function setBaseUri(?string $baseUri): OAuth2ThreeLeggedCredentials
    {
        $this->baseUri = $baseUri;
        return $this;
    }
}