<?php

namespace Perform\MailingListBundle\Connector;

use Perform\MailingListBundle\Entity\Subscriber;
use DrewM\MailChimp\MailChimp;
use Psr\Log\LoggerInterface;
use Perform\MailingListBundle\Exception\ListNotFoundException;
use Perform\MailingListBundle\Exception\ConnectorException;
use Perform\MailingListBundle\SubscriberFields;

/**
 * Add subscribers to a MailChimp list.
 *
 * @author Glynn Forrest <me@glynnforrest.com>
 **/
class MailChimpConnector implements ConnectorInterface
{
    protected $mailChimp;
    protected $logger;

    public function __construct(MailChimp $mailChimp, LoggerInterface $logger)
    {
        $this->mailChimp = $mailChimp;
        $this->logger = $logger;
        $this->attributeMap = [
            SubscriberFields::FIRST_NAME => 'FNAME',
            SubscriberFields::LAST_NAME => 'LNAME',
        ];
    }

    public function subscribe(Subscriber $subscriber)
    {
        $url = sprintf('lists/%s/members/%s', $subscriber->getList(), $this->mailChimp->subscriberHash($subscriber->getEmail()));
        $params = [
            'email_address' => $subscriber->getEmail(),
            'status' => 'subscribed',
            'merge_fields' => $this->createMergeFields($subscriber),
        ];
        $result = $this->mailChimp->put($url, $params);
        $this->logger->debug('MailChimp: PUT '.$url, $params);

        // MailChimp returns the subscriber resource on success, an error with a status code otherwise
        switch ($result['status']) {
        case 'subscribed':
            return;
        case 404:
            throw new ListNotFoundException($subscriber->getList(), __CLASS__);
        default:
            throw new ConnectorException(isset($result['detail']) ? $result['detail'] : '', __CLASS__);
        }
    }

    public function createMergeFields(Subscriber $subscriber)
    {
        $fields = [];
        foreach ($this->attributeMap as $subscriberField => $mailchimpField) {
            if ($subscriber->hasAttribute($subscriberField)) {
                $fields[$mailchimpField] = $subscriber->getAttribute($subscriberField);
            }
        }

        return $fields;
    }
}
