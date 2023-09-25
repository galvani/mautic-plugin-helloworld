<?php

declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormAuthorizeButtonInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormCallbackInterface;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\HelloWorldBundle\Form\Type\ConfigAuthType;
use MauticPlugin\HelloWorldBundle\Integration\Auth\DefaultRedirectUriTrait;
use MauticPlugin\HelloWorldBundle\Integration\HelloWorldConfiguration;
use MauticPlugin\HelloWorldBundle\Integration\HelloWorldIntegration;
use Symfony\Component\Routing\RouterInterface;

/**
 * This configures the UI for the plugin's configuration page.  The form is defined in the
 * {@see DetailsType}
 */
class ConfigSupport extends HelloWorldIntegration
    implements ConfigFormInterface, ConfigFormAuthInterface, ConfigFormAuthorizeButtonInterface, ConfigFormCallbackInterface
{
    use DefaultConfigFormTrait;

    public function getAuthConfigFormName(): string
    {
        return ConfigAuthType::class;
    }

    public function isAuthorized(): bool
    {
        return $this->configuration->isAuthorized();
    }

    public function getAuthorizationUrl(): string
    {
        return $this->configuration->getAuthorizationUrl();
    }

    public function getRedirectUri(): string
    {
        return $this->configuration->getCallbackUrl();
    }

    public function getCallbackHelpMessageTranslationKey(): string
    {
        return 'helloworld.config.callback.help';
    }
}
