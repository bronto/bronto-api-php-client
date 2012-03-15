<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/activityobject
 */
class Bronto_Api_Activity extends Bronto_Api_Object
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
    protected $_methods = array(
        'readActivities' => 'read',
    );

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
     * The primary key column or columns.
     *
     * @var mixed
     */
    protected $_primary = array('activityDate', 'trackingType');

    /**
     * @var int
     */
    protected $_iteratorType = Bronto_Api_Rowset_Iterator::TYPE_DATE;

    /**
     * @var string
     */
    protected $_iteratorParam = 'start';

    /**
     * @var string
     */
    protected $_iteratorRowField = 'activityDate';

    /**
     * For many activities, caching the row objects saves tons of time.
     * Example: fetching 1000 activities that references only 3 messageId's
     *
     * @var array
     */
    protected $_objectCache = array(
        'contact'  => array(),
        'delivery' => array(),
        'message'  => array(),
        'list'     => array(),
    );

    /**
     * @param string $startDate
     * @param int $size
     * @param string|array $types
     * @return Bronto_Api_Rowset
     */
    public function readAll($startDate = '2002-01-01T00:00:00+00:00', $size = 100, $types = array())
    {
        $filter = array(
            'start' => $startDate,
            'size'  => (int) $size
        );

        if (!empty($types)) {
            if (is_array($types)) {
                $filter['types'] = $types;
            } else {
                $filter['types'] = array($types);
            }
        }

        return parent::read(array('filter' => $filter));
    }

    /**
     * @param array $data
     */
    public function createRow(array $data = array())
    {
        throw new Bronto_Api_Activity_Exception('You cannot create an Activity row.');
    }

    /**
     * @param string $type
     * @param string $index
     * @param Bronto_Api_Row $object
     */
    public function addToCache($type, $index, Bronto_Api_Row $object)
    {
        $this->_objectCache[$type][$index] = $object;
        return $this;
    }

    /**
     * @param string $type
     * @param string $index
     * @return bool|Bronto_Api_Row
     */
    public function getFromCache($type, $index)
    {
        if (isset($this->_objectCache[$type][$index])) {
            return $this->_objectCache[$type][$index];
        }
        return false;
    }
}