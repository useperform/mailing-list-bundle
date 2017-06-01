<?php

namespace Perform\MailingListBundle\Admin;

use Perform\BaseBundle\Admin\AbstractAdmin;
use Perform\BaseBundle\Type\TypeConfig;

/**
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class LocalSubscriberAdmin extends AbstractAdmin
{
    protected $routePrefix = 'perform_mailing_list_local_subscriber_';

    public function configureTypes(TypeConfig $config)
    {
        $config
            ->add('firstName', [
                'type' => 'string',
                'contexts' => [
                    TypeConfig::CONTEXT_VIEW,
                    TypeConfig::CONTEXT_CREATE,
                    TypeConfig::CONTEXT_EDIT,
                ]
            ])
            ->add('email', [
                'type' => 'email',
            ])
            ->add('createdAt', [
                'type' => 'datetime',
                'contexts' => [
                    TypeConfig::CONTEXT_LIST,
                    TypeConfig::CONTEXT_VIEW,
                ],
                'options' => [
                    'label' => 'Sign-up date',
                ],
            ])
            ;
    }
}