<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @property-read string $id
 * @property string start
 * @property string $messageId
 * @property string $status
 * @property string $type
 * @property string $fromEmail
 * @property string $fromName
 * @property bool $authentication
 * @property bool $replyTracking
 * @property string $replyEmail
 * @property string $messageRuleId
 * @property bool $optin
 * @property float $throttle
 * @property array $content
 * @property array $recipients
 * @property array $fields
 * @property-read int $numSends
 * @property-read int $numDeliveries
 * @property-read int $numHardBadEmail
 * @property-read int $numHardDestUnreach
 * @property-read int $numHardMessageContent
 * @property-read int $numHardBounces
 * @property-read int $numSoftBadEmail
 * @property-read int $numSoftDestUnreach
 * @property-read int $numSoftMessageContent
 * @property-read int $numSoftBounces
 * @property-read int $numOtherBounces
 * @property-read int $uniqOpens
 * @property-read int $numOpens
 * @property-read int $avgOpens
 * @property-read int $uniqClicks
 * @property-read int $numClicks
 * @property-read int $avgClicks
 * @property-read int $uniqConversions
 * @property-read int $numConversions
 * @property-read int $avgConversions
 * @property-read int $revenue
 * @property-read int $numSurveyResponses
 * @property-read int $numFriendForwards
 * @property-read int $numContactUpdates
 * @property-read int $numUnsubscribesByPrefs
 * @property-read int $numUnsubscribesByComplaint
 * @property-read int $numContactLoss
 * @property-read int $numContactLossBounces
 * @property-read float $deliveryRate
 * @property-read float $openRate
 * @property-read float $clickRate
 * @property-read float $clickThroughRate
 * @property-read float $conversionRate
 * @property-read float $bounceRate
 * @property-read float $complaintRate
 * @property-read float $contactLossRate
 * @property-read int $numSocialShares
 * @property-read int $sharesFacebook
 * @property-read int $sharesTwitter
 * @property-read int $sharesLinkedIn
 * @property-read int $sharesDigg
 * @property-read int $sharesMySpace
 * @property-read int $viewsFacebook
 * @property-read int $viewsTwitter
 * @property-read int $viewsLinkedIn
 * @property-read int $viewsDigg
 * @property-read int $viewsMySpace
 * @property-read int $numSocialViews
 * @method Bronto_Api_Delivery_Row read() read()
 * @method Bronto_Api_Delivery_Row delete() delete()
 * @method Bronto_Api_Delivery getApiObject() getApiObject()
 */
class Bronto_Api_Delivery_Row extends Bronto_Api_Row
{
    /**
     * @var array
     */
    protected $_recipients = array();

    /**
     * @param bool $refresh
     * @param array $additionalFilter
     * @return array
     */
    public function getRecipients($refresh = false, array $additionalFilter = array())
    {
        if (!$this->id) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("This Delivery has not been retrieved yet (has no DeliveryId)");
        }

        // If we have already retrieved this, don't do it again
        if (!empty($this->_recipients) && !$refresh) {
            return $this->_recipients;
        }

        // We didn't do $includeRecipients = true from original request
        if (empty($this->recipients)) {
            $this->recipients = array();

            $filter = array();
            $filter['deliveryId'] = $this->id;
            $filter = array_merge($additionalFilter, $filter);
            $recipientPage = 1;
            while ($recipients = $this->getApiObject()->readDeliveryRecipients($filter, $recipientPage)) {
                if (!$recipients->count()) {
                    break;
                }

                $this->recipients = $this->recipients + $recipients;
                $recipientPage++;
            }
        }

        $this->_recipients = array();
        if (!empty($this->recipients)) {
            foreach ($this->recipients as $i => $recipient) {
                switch ($recipient->type) {
                    case 'list':
                        $listObject = $this->getApi()->getListObject();
                        $list = $listObject->createRow();
                        $list->id = $recipient->id;
                        $this->_recipients[] = $list;
                        break;
                    case 'contact':
                        $contactObject = $this->getApi()->getContactObject();
                        $contact = $contactObject->createRow();
                        $contact->id = $recipient->id;
                        $this->_recipients[] = $contact;
                        break;
                    case 'segment':
                        $segmentObject = $this->getApi()->getSegmentObject();
                        $segment = $segmentObject->createRow();
                        $segment->id = $recipient->id;
                        $this->_recipients[] = $segment;
                        break;
                    default:
                        $exceptionClass = $this->getExceptionClass();
                        throw new $exceptionClass("Recipient type '{$recipient->type}' is not currently supported");
                        break;
                }
            }
        }

        return $this->_recipients;
    }

    /**
     * @param Bronto_Api_DeliveryGroup_Row|string $$deliveryGroup
     * @return bool
     */
    public function addToDeliveryGroup($deliveryGroup)
    {
        if (!$this->id) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("This Delivery has not been saved yet (has no DeliveryId)");
        }

        $deliveryGroupId = $deliveryGroup;
        if ($deliveryGroup instanceOf Bronto_Api_DeliveryGroup_Row) {
            if (!$deliveryGroup->id) {
                $deliveryGroup = $deliveryGroup->read();
            }
            $deliveryGroupId = $deliveryGroup->id;
        }

        if (empty($deliveryGroupId)) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass('Unable to find deliveryGroup');
        }

        $deliveryGroupObject = $this->getApi()->getDeliveryGroupObject();
        return $deliveryGroupObject->addToDeliveryGroup($deliveryGroupId, array($this->id));
    }

    /**
     * Sets a value for a Message Field
     *
     * @param string $$field
     * @param mixed $value
     * @param string $$type text|html
     * @return Bronto_Api_Delivery_Row
     */
    public function setField($field, $value, $type = 'html')
    {
        if (strlen($field) > 25) {
            // Make sure we don't pass a field name longer than 25 characters
            $field = substr($field, 0, 25);
        }

        $messageField = array(
            'name'    => $field,
            'type'    => $type,
            'content' => $value,
        );

        if (!isset($this->_data['fields']) || !is_array($this->_data['fields'])) {
            $this->_data['fields'] = array();
        } else {
            // Check for dupes
            foreach ($this->_data['fields'] as $i => $_field) {
                if ($_field['name'] == $messageField['name']) {
                    $this->_data['fields'][$i] = $messageField;
                    $this->_modifiedFields['fields'] = true;
                    return $this;
                }
            }
        }

        $this->_data['fields'][] = $messageField;
        $this->_modifiedFields['fields'] = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        if (!empty($this->_data['fields'])) {
            return $this->_data['fields'];
        }
        return array();
    }

    /**
     * @param bool $upsert Ignored
     * @param bool $refresh
     * @return Bronto_Api_Delivery_Row
     */
    public function save($upsert = null, $refresh = false)
    {
        parent::_save(false, $refresh);
        return $this;
    }
}
