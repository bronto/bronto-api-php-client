<?php

class Bronto_Api_Rowset_Iterator implements Iterator
{
    const TYPE_PAGE = 1;
    const TYPE_DATE = 2;

    /**
     * @var Bronto_Api_Rowset_Abstract
     */
    protected $_rowset;

    /**
     * @var int
     */
    protected $_page = 1;

    /**
     * @var int
     */
    protected $_type;

    /**
     * @var string
     */
    protected $_checkField;

    /**
     * @var string
     */
    protected $_updateField;

    /**
     * @var int
     */
    protected $_initialFieldValue;

    /**
     * @var int
     */
    protected $_lastFieldValue;

    /**
     * @var int
     */
    protected $_nextFieldValue;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(Bronto_Api_Rowset_Abstract $rowset)
    {
        $apiObject = $rowset->getApiObject();

        if (!$apiObject->canIterate()) {
            throw new Exception(sprintf('Cannot iterate results for %s', $apiObject->getName()));
        }

        $this->_type        = $apiObject->getIteratorType();
        $this->_checkField  = $apiObject->getIteratorCheckField();
        $this->_updateField = $apiObject->getIteratorUpdateField();
        $this->_rowset      = $rowset;

        // Set initial/next values
        $params = $this->_rowset->getParams();
        if (isset($params[$this->_checkField])) {
            $this->_setupInitialValues($params[$this->_checkField]);
        } else {
            // @todo
        }

        if (empty($this->_nextFieldValue)) {
            throw new Exception('Could not determine next field value');
        }
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return bool
     */
    protected function _setupInitialValues($value)
    {
        if ($this->_type == self::TYPE_DATE) {
            $this->_nextFieldValue    = DateTime::createFromFormat(DateTime::ISO8601, $value);
            $this->_lastFieldValue    = $this->_nextFieldValue;
            $this->_initialFieldValue = $this->_nextFieldValue;
        } elseif ($this->_type == self::TYPE_PAGE) {
            $this->_initialFieldValue = $value;
            $this->_lastFieldValue    = $value;
            $this->_nextFieldValue    = $value + 1;
        }
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Bronto_Api_Rowset_Iterator Fluent interface.
     */
    public function rewind()
    {
        return $this;
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
            $checkField = $this->_checkField;
            $checkValue = $row->{$checkField};
            if (!empty($checkValue)) {
                $dateTime = DateTime::createFromFormat("Y-m-d\TH:i:s.uO", $checkValue);
                if ($dateTime > $this->_nextFieldValue) {
                    $this->_nextFieldValue = $dateTime;
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
        if ($this->_nextFieldValue <= $this->_lastFieldValue) {
            return false;
        }

        $params = $this->_updateParams(null, $this->_rowset->getParams());
        $this->_rowset = $this->_rowset->getApiObject()->read($params);
        $this->_page   = $this->_page + 1;
        if ($this->_type == self::TYPE_PAGE) {
            $this->_nextFieldValue = $this->_nextFieldValue + 1;
        }
        return $this->_rowset->valid();
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return array
     */
    protected function _updateParams($key, $value)
    {
        if (is_array($value)) {
            foreach ($value as $key1 => $value1) {
                $value[$key1] = $this->_updateParams($key1, $value1);
            }
        } else {
            if ($key == $this->_updateField) {
                $this->_lastFieldValue = $this->_nextFieldValue;
                if ($this->_type == self::TYPE_DATE) {
                    $value = $this->_nextFieldValue->format("Y-m-d\TH:i:sP");
                } elseif ($this->_type == self::TYPE_PAGE) {
                    $value = $this->_nextFieldValue;
                }
            }
        }
        return $value;
    }
}