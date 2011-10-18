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
 */
class Bronto_Api_Delivery_Row extends Bronto_Api_Row
{
    /** Status */
    const STATUS_SENT     = 'sent';
    const STATUS_SENDING  = 'sending';
    const STATUS_UNSENT   = 'unsent';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_SKIPPED  = 'skipped';

    /** Type */
    const TYPE_NORMAL        = 'normal';
    const TYPE_TEST          = 'test';
    const TYPE_TRANSACTIONAL = 'transactional';
    const TYPE_AUTOMATED     = 'automated';

    /**
     * @var array
     */
    protected $_options = array(
        'status' => array(
            self::STATUS_SENT,
            self::STATUS_SENDING,
            self::STATUS_UNSENT,
            self::STATUS_ARCHIVED,
            self::STATUS_SKIPPED,
        ),
        'type' => array(
            self::TYPE_NORMAL,
            self::TYPE_TEST,
            self::TYPE_TRANSACTIONAL,
            self::TYPE_AUTOMATED,
        ),
    );

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
     * @param string $type
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
        }

        $this->_data['fields'][] = $messageField;
        $this->_modifiedFields['fields'] = true;
        return $this;
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
     * @return Bronto_Api_Delivery_Row
     */
    public function save($upsert = false, $refresh = true)
    {
        /**
         * If the _cleanData array is empty,
         * this is an ADD of a new row.
         * Otherwise it is an UPDATE.
         */
        if (empty($this->_cleanData)) {
            return parent::save(false, $refresh);
        } else {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass('Cannot update a delivery record');
        }
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Delivery
     */
    public function getApiObject()
    {
        return parent::getApiObject();
    }
}