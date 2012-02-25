<?php

/** @var Bronto_Api_Delivery_Row */
require_once 'Bronto/Api/Delivery/Row.php';

/** @var Bronto_Api_Delivery_Exception */
require_once 'Bronto/Api/Delivery/Exception.php';

/**
 * @method Bronto_Api_Delivery_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Delivery extends Bronto_Api_Abstract
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
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Deliveries';

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Delivery_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Delivery_Exception';

    /**
     * @param array $filter
     * @param bool $includeRecipients
     * @param bool $includeContent
     * @param int $pageNumber
     * @throws Bronto_Api_Delivery_Exception
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
     * @param string $startDate
     * @param bool $includeRecipients
     * @param bool $includeContent
     * @param int $pageNumber
     * @param array $additionalFilter
     * @throws Bronto_Api_Delivery_Exception
     * @return Bronto_Api_Rowset
     */
    public function readAllAfterDate($startDate, $includeRecipients = false, $includeContent = false, $pageNumber = 1, array $additionalFilter = array())
    {
        $filter = array(
            'start' => array(
                'operator' => 'After',
                'value'    => $startDate,
            )
        );
        $filter = array_merge($additionalFilter, $filter);
        return $this->readAll($filter, $includeRecipients, $includeContent, $pageNumber);
    }

    /**
     * @param array $filter
     * @param int $pageNumber
     * @return array
     */
    public function readDeliveryRecipients(array $filter = array(), $pageNumber = 1)
    {
        $params = array();
        $params['filter']     = $filter;
        $params['pageNumber'] = (int) $pageNumber;

        try {
            $client = $this->getApi()->getSoapClient();
            $result = $client->readDeliveryRecipients($params)->return;
        } catch (Exception $e) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass($e->getMessage());
        }

        if (!isset($result->return)) {
            $result->return = array();
        }

        return $result->return;
    }
}