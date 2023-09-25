<?php

declare(strict_types=1);

namespace MauticPlugin\HelloWorldBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class EpathType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'size',
            ChoiceType::class,
            [
                'choices' => [
                    'mautic.epath.option.size.normal'  => 'normal',
                    'mautic.epath.option.size.compact' => 'compact',
                ],
                'label' => 'mautic.epath.tooltip.size',
                'data'  => $options['data']['size'] ?? false,
            ]
        );

        $builder->add(
            'buttons',
            FormButtonsType::class,
            [
                'apply_text'     => false,
                'save_text'      => 'mautic.core.form.submit',
                'cancel_onclick' => 'javascript:void(0);',
                'cancel_attr'    => [
                    'data-dismiss' => 'modal',
                ],
            ]
        );

        if (!empty($options['action'])) {
            $builder->setAction($options['action']);
        }
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'epath';
    }
}
