<?php

/**
 * @property-read string id
 * @property date start
 * @property string messageId
 * @property string status
 * @property string type
 * @property string fromEmail
 * @property string fromName
 * @property bool authentication
 * @property bool replyTracking
 * @property string replyEmail
 * @property string messageRuleId
 * @property bool optin
 * @property array content
 * @property array recipients
 * @property array fields
 * @property int numSends
 * @property int numDeliveries
 * @property int numHardBadEmail
 * @property int numHardDestUnreach
 * @property int numHardMessageContent
 * @property int numHardBounces
 * @property int numSoftBadEmail
 * @property int numSoftDestUnreach
 * @property int numSoftMessageContent
 * @property int numSoftBounces
 * @property int numOtherBounces
 * @property int uniqOpens
 * @property int numOpens
 * @property int avgOpens
 * @property int uniqClicks
 * @property int numClicks
 * @property int avgClicks
 * @property int uniqConversions
 * @property int numConversions
 * @property int avgConversions
 * @property int revenue
 * @property int numSurveyResponses
 * @property int numFriendForwards
 * @property int numContactUpdates
 * @property int numUnsubscribesByPrefs
 * @property int numUnsubscribesByComplaint
 * @property int numContactLoss
 * @property int numContactLossBounces
 * @property float deliveryRate
 * @property float openRate
 * @property float clickRate
 * @property float clickThroughRate
 * @property float conversionRate
 * @property float bounceRate
 * @property float complaintRate
 * @property float contactLossRate
 * @property int numSocialShares
 * @property int sharesFacebook
 * @property int sharesTwitter
 * @property int sharesLinkedIn
 * @property int sharesDigg
 * @property int sharesMySpace
 * @property int viewsFacebook
 * @property int viewsTwitter
 * @property int viewsLinkedIn
 * @property int viewsDigg
 * @property int viewsMySpace
 * @property int numSocialViews
 * @method Bronto_Api_Delivery getApiObject()
 */
class Bronto_Api_Delivery_Row extends Bronto_Api_Row
{
    /**
     * @return array
     */
    public function getRecipients()
    {
        $recipients = array();
        if (!empty($this->recipients)) {
            foreach ($this->recipients as $i => $recipient) {
                switch ($recipient->type) {
                    case 'list':
                        $listObject = $this->getApiObject()->getApi()->getListObject();
                        $list = $listObject->createRow();
                        $list->id = $recipient->id;
                        $recipients[$i] = $list;
                        break;
                    default:
                        $exceptionClass = $this->getExceptionClass();
                        throw new $exceptionClass("Recipient type '{$recipient->type}' is not currently supported");
                        break;
                }
            }
        }
        return $recipients;
    }

    /**
     * @param Bronto_Api_Deliverygroup_Row|string $deliveryGroup
     * @return bool
     */
    public function addToDeliveryGroup($deliveryGroup)
    {
        if (!$this->id) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("This Delivery has not been saved yet (has no DeliveryId)");
        }

        $deliveryGroupId = $deliveryGroup;
        if ($deliveryGroup instanceOf Bronto_Api_Deliverygroup_Row) {
            if (!$deliveryGroup->id) {
                $deliveryGroup = $deliveryGroup->read();
            }
            $deliveryGroupId = $deliveryGroup->id;
        }

        $deliveryGroupObject = $this->getApiObject()->getApi()->getDeliveryGroupObject();
        return $deliveryGroupObject->addToDeliveryGroup($deliveryGroupId, array($this->id));
    }

    /**
     * Sets a value for a Message Field
     *
     * @param string $field
     * @param mixed $value
     * @param string $type text|html
     * @return Bronto_Api_Delivery_Row
     */
    public function setField($field, $value, $type = 'html')
    {
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
     * @param bool $returnData
     * @return Bronto_Api_Delivery_Row|array
     */
    public function read($returnData = false)
    {
        $params = array('id' => $this->id);
        return parent::_read($params, $returnData);
    }

    /**
     * @param bool $upsert
     * @param bool $refresh
     * @return Bronto_Api_Delivery_Row
     */
    public function save($upsert = false, $refresh = true)
    {
        return parent::save($upsert, $refresh);
    }
}