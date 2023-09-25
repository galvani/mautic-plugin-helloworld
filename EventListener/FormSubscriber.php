<?php

declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\EventListener;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\Event\ValidationEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\LeadBundle\Event\LeadEvent;
use Mautic\LeadBundle\LeadEvents;
use Mautic\LeadBundle\Model\LeadModel;
use MauticPlugin\HelloWorldBundle\Form\Type\EpathType;
use MauticPlugin\HelloWorldBundle\Integration\HelloWorldConfiguration;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private HelloWorldConfiguration  $config,
        private EventDispatcherInterface $eventDispatcher,
        private LeadModel                $leadModel,
        private TranslatorInterface      $translator,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::FORM_ON_BUILD         => ['onFormBuild', 0],
        ];
    }

    public function onFormBuild(FormBuilderEvent $event): void
    {
        if (!$this->config->isPublished()) {
            return;
        }

        $event->addFormField('plugin.epath', [
            'label'          => 'mautic.epath.action',
            'formType'       => EpathType::class,
            'template'       => 'EpathBundle::epath.html.php',
            'builderOptions' => [
                'addLeadFieldList' => false,
                'addIsRequired'    => false,
                'addDefaultValue'  => false,
                'addSaveResult'    => true,
            ],
            'site_key' => $this->config->getApiKeys()['site_key'],
        ]);

//        $event->addValidator('plugin.epath.validator', [
//            'eventName' => EpathEvents::ON_FORM_VALIDATE,
//            'fieldType' => 'plugin.epath',
//        ]);
    }

    public function onFormValidate(ValidationEvent $event)
    {
        if (!$this->config->isPublished()) {
            return;
        }

        $event->failedValidation($this->translator->trans('mautic.integration.epath.failure_message'));

        // This comes fully from reCaptchaBundle, seems right to delete the lead if it seems to be a bot
        $this->eventDispatcher->addListener(LeadEvents::LEAD_POST_SAVE, function (LeadEvent $event) {
            if ($event->isNew()) {
                $this->leadModel->deleteEntity($event->getLead());
            }
        }, -255);
    }
}
