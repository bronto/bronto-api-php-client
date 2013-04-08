<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @link http://community.bronto.com/api/v4/objects/general/deliveryobject
 *
 * @method Bronto_Api_Delivery_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Delivery extends Bronto_Api_Object
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
    protected $_methods = array(
        'addDeliveries'          => 'add',
        'readDeliveries'         => 'read',
        'updateDeliveries'       => 'update',
        'deleteDeliveries'       => 'delete',
        'readDeliveryRecipients' => true,
    );

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
     * @param array $filter
     * @param bool $includeRecipients
     * @param bool $includeContent
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), $includeRecipients = false, $includeContent = false, $pageNumber = 1)
    {
        $params = array();
        $params['filter']            = $filter;
        $params['includeRecipients'] = (bool) $includeRecipients;
        $params['includeContent']    = (bool) $includeContent;
        $params['pageNumber']        = (int)  $pageNumber;
        return $this->read($params);
    }

    /**
     * @param array $filter
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readDeliveryRecipients(array $filter = array(), $pageNumber = 1)
    {
        $params = array();
        $params['filter']     = $filter;
        $params['pageNumber'] = (int) $pageNumber;
        return $this->doRequest('readDeliveryRecipients', $params);
    }
}
