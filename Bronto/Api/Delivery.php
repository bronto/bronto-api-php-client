<?php

/** @var Bronto_Api_Delivery_Row */
require_once 'Bronto/Api/Delivery/Row.php';

/** @var Bronto_Api_Delivery_Exception */
require_once 'Bronto/Api/Delivery/Exception.php';

class Bronto_Api_Delivery extends Bronto_Api_Abstract
{
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
        return parent::readAll($params);
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
     * @param array $data
     * @throws Bronto_Api_Delivery_Exception
     * @return Bronto_Api_Delivery_Row
     */
    public function createRow(array $data = array())
    {
        return parent::createRow($data);
    }
}