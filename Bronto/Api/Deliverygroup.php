<?php

/** @var Bronto_Api_Deliverygroup_Row */
require_once 'Bronto/Api/Deliverygroup/Row.php';

/** @var Bronto_Api_Deliverygroup_Exception */
require_once 'Bronto/Api/Deliverygroup/Exception.php';

/**
 * @method Bronto_Api_Deliverygroup_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Deliverygroup extends Bronto_Api_Abstract
{
    /** Visibility */
    const VISIBILITY_INTERNAL = 'INTERNAL';
    const VISIBILITY_PUBLIC   = 'PUBLIC';

    /**
     * @var array
     */
    protected $_options = array(
        'visibility' => array(
            self::VISIBILITY_INTERNAL,
            self::VISIBILITY_PUBLIC,
        ),
    );

    /**
     * The object name.
     *
     * @var string
     */
    protected $_name     = 'DeliveryGroup';
    protected $_nameRead = 'DeliveryGroups';

    /**
     * Whether or not this object has an addOrUpdate method
     *
     * @var bool
     */
    protected $_hasUpsert = true;

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Deliverygroup_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Deliverygroup_Exception';

    /**
     * @param array $filter
     * @param int $pageNumber
     * @throws Bronto_Api_Deliverygroup_Exception
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), $pageNumber = 1)
    {
        $params = array();
        $params['filter']     = $filter;
        $params['pageNumber'] = (int) $pageNumber;
        return $this->read($params);
    }

    /**
     * @param string $deliveryGroupId
     * @param array $deliveryIds
     * @param array $messageIds
     * @param array $messageRuleIds
     * @return bool
     */
    public function addToDeliveryGroup($deliveryGroupId, array $deliveryIds = array(), array $messageIds = array(), array $messageRuleIds = array())
    {
        $data = array(
            'deliveryGroup' => array('id' => $deliveryGroupId),
        );

        if (!empty($deliveryIds)) {
            $data['deliveryIds'] = $deliveryIds;
        }

        if (!empty($messageIds)) {
            $data['messageIds'] = $messageIds;
        }

        if (!empty($messageRuleIds)) {
            $data['messageRuleIds'] = $messageRuleIds;
        }

        return $this->_doRequest('addToDeliveryGroup', $data);
    }
}