<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @link http://community.bronto.com/api/v4/objects/general/activityobject
 */
class Bronto_Api_Activity extends Bronto_Api_Object
{
    /** trackingType */
    const TYPE_OPEN        = 'open';
    const TYPE_CLICK       = 'click';
    const TYPE_CONVERSION  = 'conversion';
    const TYPE_BOUNCE      = 'bounce';
    const TYPE_SEND        = 'send';
    const TYPE_UNSUBSCRIBE = 'unsubscribe';
    const TYPE_VIEW        = 'view';

    /** bounceType */
    const BOUNCE_HARD_CONN_PERM    = 'conn_perm';
    const BOUNCE_HARD_SUB_PERM     = 'sub_perm';
    const BOUNCE_HARD_CONTENT_PERM = 'content_perm';
    const BOUNCE_SOFT_CONN_TEMP    = 'conn_temp';
    const BOUNCE_SOFT_SUB_TEMP     = 'sub_temp';
    const BOUNCE_SOFT_CONTENT_TEMP = 'content_temp';
    const BOUNCE_SOFT_OTHER        = 'other';

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
        'readDirection' => array(
            self::DIRECTION_FIRST,
            self::DIRECTION_NEXT,
        ),
        'bounceType' => array(
            self::BOUNCE_HARD_CONN_PERM,
            self::BOUNCE_HARD_SUB_PERM,
            self::BOUNCE_HARD_CONTENT_PERM,
            self::BOUNCE_SOFT_CONN_TEMP,
            self::BOUNCE_SOFT_SUB_TEMP,
            self::BOUNCE_SOFT_CONTENT_TEMP,
            self::BOUNCE_SOFT_OTHER,
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
    protected $_iteratorType = Bronto_Api_Rowset_Iterator::TYPE_STREAM;

    /**
     * The key(s) to use when paginating
     *
     * @var array
     */
    protected $_iteratorParams = array(
        'readDirection' => false,
        'start'         => 'activityDate',
    );

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
     * @param string $direction
     * @param string|array $contactIds
     * @return Bronto_Api_Rowset
     */
    public function readAll($startDate = '2002-01-01T00:00:00+00:00', $size = 1000, $types = array(), $direction = self::DIRECTION_FIRST, $contactIds = array())
    {
        $filter = array(
            'start'         => '2002-01-01T00:00:00+00:00',
            'size'          => 1000,
            'types'         => $this->getOptionValues('trackingType'),
            'readDirection' => self::DIRECTION_FIRST,
            'contactIds'    => array(),
        );

        if (!empty($startDate)) {
            $filter['start'] = $startDate;
        }

        if (!empty($size)) {
            $filter['size'] = $size < 1000 ? 1000 : (int) $size;
        }

        if (!empty($types)) {
            if (is_array($types)) {
                $filter['types'] = $types;
            } else {
                $filter['types'] = array($types);
            }
        }

        $direction = strtoupper($direction);
        if (in_array($direction, $this->_options['readDirection'])) {
            $filter['readDirection'] = $direction;
        }

        if (!empty($contactIds)) {
            if (is_array($contactIds)) {
                $filter['contactIds'] = $contactIds;
            } else {
                $filter['contactIds'] = array($contactIds);
            }
        }

        // @todo Remove if the contactIds filter is enabled again
        unset($filter['contactIds']);

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
        // Conserve memory
        while (count($this->_objectCache[$type]) >= 25) {
            array_shift($this->_objectCache[$type]);
        }

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

    /**
     * @param string $bounceType
     * @return bool
     */
    public function isBounceHard($bounceType)
    {
        return (
            $bounceType === self::BOUNCE_HARD_CONN_PERM ||
            $bounceType === self::BOUNCE_HARD_SUB_PERM ||
            $bounceType === self::BOUNCE_HARD_CONTENT_PERM
        );
    }

    /**
     * @param string $bounceType
     * @return bool
     */
    public function isBounceSoft($bounceType)
    {
        return (
            $bounceType === self::BOUNCE_SOFT_CONN_TEMP ||
            $bounceType === self::BOUNCE_SOFT_SUB_TEMP ||
            $bounceType === self::BOUNCE_SOFT_CONTENT_TEMP ||
            $bounceType === self::BOUNCE_SOFT_OTHER
        );
    }
}
