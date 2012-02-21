<?php

/** @var Bronto_Api_Activity_Row */
require_once 'Bronto/Api/Activity/Row.php';

/** @var Bronto_Api_Activity_Exception */
require_once 'Bronto/Api/Activity/Exception.php';

class Bronto_Api_Activity extends Bronto_Api_Abstract
{
    /** Type */
    const TYPE_OPEN        = 'open';
    const TYPE_CLICK       = 'click';
    const TYPE_CONVERSION  = 'conversion';
    const TYPE_BOUNCE      = 'bounce';
    const TYPE_SEND        = 'send';
    const TYPE_UNSUBSCRIBE = 'unsubscribe';
    const TYPE_VIEW        = 'view';

    /**
     * @var array
     */
    protected $_options = array(
        'trackingType' => array(
            self::TYPE_OPEN,
            self::TYPE_CLICK,
            self::TYPE_CONVERSION,
            self::TYPE_BOUNCE,
            self::TYPE_SEND,
            self::TYPE_UNSUBSCRIBE,
            self::TYPE_VIEW,
        ),
    );

    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Activities';

    /**
     * The primary key column or columns.
     *
     * @var mixed
     */
    protected $_primary = array('activityDate', 'trackingType');

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Activity_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Activity_Exception';

    /**
     * @param string $startDate
     * @param int $size
     * @param mixed $types
     * @throws Bronto_Api_Activity_Exception
     * @return Bronto_Api_Rowset
     */
    public function readAll($startDate, $size = 25, $types = array())
    {
        $filter = array();
        $filter['start'] = $startDate;
        $filter['size']  = (int) $size;
        if (!empty($types)) {
            if (is_array($types)) {
                $filter['types'] = $types;
            } else {
                $filter['types'] = array($types);
            }
        }
        return parent::readAll(array('filter' => $filter));
    }

    /**
     * @param array $data
     * @throws Bronto_Api_Activity_Exception
     * @return void
     */
    public function createRow(array $data = array())
    {
        $exceptionClass = $this->getExceptionClass();
        throw new $exceptionClass('You cannot create an Activity row.');
    }
}