<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @link http://community.bronto.com/api/v4/objects/general/deliverygroupobject
 *
 * @method Bronto_Api_DeliveryGroup_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_DeliveryGroup extends Bronto_Api_Object
{
    /** Visibility */
    const VISIBILITY_INTERNAL = 'INTERNAL';
    const VISIBILITY_PUBLIC   = 'PUBLIC';

    /** memberType */
    const MEMBER_TYPE_DELIVERIES     = 'DELIVERIES';
    const MEMBER_TYPE_AUTOMATORS     = 'AUTOMATORS';
    const MEMBER_TYPE_MESSAGEGROUPS  = 'MESSAGEGROUPS';
    const MEMBER_TYPE_DELIVERYGROUPS = 'DELIVERYGROUPS';

    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'DeliveryGroup';

    /**
     * @var array
     */
    protected $_methods = array(
        'addDeliveryGroup'         => 'add',
        'readDeliveryGroups'       => 'read',
        'updateDeliveryGroup'      => 'update',
        'deleteDeliveryGroup'      => 'delete',
        'addOrUpdateDeliveryGroup' => 'addOrUpdate',
        'addToDeliveryGroup'       => true,
        'deleteFromDeliveryGroup'  => true,
    );

    /**
     * @var array
     */
    protected $_options = array(
        'visibility' => array(
            self::VISIBILITY_INTERNAL,
            self::VISIBILITY_PUBLIC,
        ),
        'memberType' => array(
            self::MEMBER_TYPE_DELIVERIES,
            self::MEMBER_TYPE_AUTOMATORS,
            self::MEMBER_TYPE_MESSAGEGROUPS,
            self::MEMBER_TYPE_DELIVERYGROUPS,
        ),
    );

    /**
     * @param array $filter
     * @param int $pageNumber
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
     * @return Bronto_Api_Rowset
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

        return $this->doRequest('addToDeliveryGroup', $data);
    }
}
