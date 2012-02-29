<?php

class Bronto_Api_Rowset_Iterator implements Iterator, Countable
{
    const TYPE_PAGE = 1;
    const TYPE_DATE = 2;

    /**
     * API Object
     *
     * @var Bronto_Api_Abstract
     */
    protected $_apiObject;

    /**
     * @var Bronto_Api_Rowset_Abstract
     */
    protected $_rowset;

    /**
     * @var int
     */
    protected $_page = 1;

    /**
     * @var bool
     */
    protected $_newPage = true;

    /**
     * How many data rows there are (currently)
     *
     * @var int
     */
    protected $_count;

    /**
     * Iterator pointer
     *
     * @var integer
     */
    protected $_pointer = 0;

    /**
     * @var int
     */
    protected $_type;

    /**
     * Query field for paging
     *
     * @var string
     */
    protected $_param;

    /**
     * @var string
     */
    protected $_rowField;

    /**
     * Initial paging value
     *
     * @var int|string
     */
    protected $_initialParamValue;

    /**
     * Last used paging value
     *
     * @var int|string
     */
    protected $_lastParamValue;

    /**
     * Next paging value to be used
     *
     * @var int|string
     */
    protected $_nextParamValue;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(Bronto_Api_Rowset_Abstract $rowset)
    {
        $this->_apiObject = $rowset->getApiObject();

        if (!$this->_apiObject->canIterate()) {
            throw new Bronto_Api_Rowset_Exception(sprintf('Cannot iterate results for %s', $this->_apiObject->getName()));
        }

        $this->_type     = $this->_apiObject->getIteratorType();
        $this->_param    = $this->_apiObject->getIteratorParam();
        $this->_rowField = $this->_apiObject->getIteratorRowField();
        $this->_rowset   = $rowset;
        $this->_count    = $rowset->count();

        // Set initial/next values
        $params = $this->_rowset->getParams();
        if (isset($params[$this->_param])) {
            $this->_setupInitialValues($params[$this->_param]);
        } else {
            foreach ($params as $value) {
                if (is_array($value) && isset($value[$this->_param])) {
                    $this->_setupInitialValues($value[$this->_param]);
                }
            }
        }

        if (empty($this->_nextParamValue)) {
            throw new Bronto_Api_Rowset_Exception('Could not determine next field value');
        }
    }

    /**
     * @return Bronto_Api_Abstract
     */
    public function getApiObject()
    {
        return $this->_apiObject;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    protected function _setupInitialValues($value)
    {
        if ($this->_type == self::TYPE_DATE) {
            $this->_nextParamValue    = DateTime::createFromFormat(DateTime::ISO8601, $value);
            $this->_lastParamValue    = clone $this->_nextParamValue;
            $this->_initialParamValue = clone $this->_nextParamValue;
            $this->_hashList          = array();
        } elseif ($this->_type == self::TYPE_PAGE) {
            $this->_initialParamValue = $value;
            $this->_lastParamValue    = $value;
            $this->_nextParamValue    = $value + 1;
        }
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_page;
    }

    /**
     * @return int
     */
    public function getCurrentKey()
    {
        return $this->_pointer;
    }

    /**
     * Returns the number of elements in the collection.
     *
     * Implements Countable::count()
     *
     * @return int
     */
    public function count()
    {
        return $this->_count;
    }

    /**
     * Required by interface Iterator.
     *
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * Return the current element.
     * Similar to the current() function for arrays in PHP
     * Required by interface Iterator.
     *
     * @return Bronto_Api_Row_Abstract current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        /* @var $row Bronto_Api_Row_Abstract */
        $row = $this->_rowset->current();

        if ($this->_type == self::TYPE_DATE) {
            $rowField = $this->_rowField;
            $rowValue = $row->{$rowField};
            if (!empty($rowValue)) {
                $dateTime = DateTime::createFromFormat(DateTime::ISO8601, $rowValue);
                if ($dateTime > $this->_nextParamValue) {
                    $this->_nextParamValue = $dateTime;
                }
            }
        }

        return $row;
    }

    /**
     * Return the identifying key of the current element.
     * Similar to the key() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return int
     */
    public function key()
    {
        return $this->_rowset->key();
    }

    /**
     * Move forward to next element.
     * Similar to the next() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return void
     */
    public function next()
    {
        $this->_rowset->next();
        ++$this->_pointer;
        $this->_newPage = false;
        return $this->current();
    }

    /**
     * Check if there is a current element after calls to rewind() or next().
     * Used to check if we've iterated to the end of the collection.
     * Required by interface Iterator.
     *
     * @return bool False if there's nothing more to iterate over
     */
    public function valid()
    {
        if ($this->_rowset->valid()) {
            return true;
        }

        return $this->_nextRowset();
    }

    /**
     * @return bool
     */
    protected function _nextRowset()
    {
        // Special check for DATE type so we don't infinite loop
        if ($this->_type == self::TYPE_DATE) {
            if ($this->_nextParamValue <= $this->_lastParamValue) {
                // We have to add 1 second when next value is == last
                $this->_nextParamValue->add(new DateInterval('PT1S'));
            }
        }

        // Set params from next request
        $params = $this->_rowset->getParams();
        if (isset($params[$this->_param])) {
            $params[$this->_param] = $this->_nextParamValue;
        } else {
            foreach ($params as &$value) {
                if (is_array($value) && isset($value[$this->_param])) {
                    $value[$this->_param] = $this->_nextParamValue;
                    if ($this->_type == self::TYPE_DATE) {
                        $value[$this->_param] = date('c', $this->_nextParamValue->getTimestamp());
                    }
                }
            }
        }

        // Make request for the new rowset
        unset($this->_rowset);
        $this->_rowset = $this->getApiObject()->read($params);

        // Increments
        $this->_newPage = true;
        $this->_page    = $this->_page + 1;
        $this->_count   = $this->_count + $this->_rowset->count();

        if ($this->_type == self::TYPE_PAGE) {
            $this->_lastParamValue = $this->_nextParamValue;
            $this->_nextParamValue = $this->_nextParamValue + 1;
        } else {
            $this->_lastParamValue = clone $this->_nextParamValue;
        }

        return $this->_rowset->valid();
    }

    /**
     * @return bool
     */
    public function isNewPage()
    {
        return $this->_newPage;
    }

    /**
     * @return mixed
     */
    public function getLastPageFilter()
    {
        if ($this->_lastParamValue instanceOf DateTime) {
            return $this->_lastParamValue->format(DateTime::ISO8601);
        }
        return $this->_lastParamValue;
    }
}