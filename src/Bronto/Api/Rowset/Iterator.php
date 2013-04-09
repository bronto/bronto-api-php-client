<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 */
class Bronto_Api_Rowset_Iterator implements Iterator, Countable
{
    /** Iterator Types */
    const TYPE_NONE   = 0;
    const TYPE_PAGE   = 1;
    const TYPE_DATE   = 2;
    const TYPE_STREAM = 3;

    /**
     * API Object
     *
     * @var Bronto_Api_Object
     */
    protected $_apiObject;

    /**
     * @var Bronto_Api_Rowset
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
    protected $_count = 0;

    /**
     * Iterator pointer
     *
     * @var integer
     */
    protected $_pointer = 0;

    /**
     * @var int
     */
    protected $_type = self::TYPE_NONE;

    /**
     * Query field(s) for paging
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Initial paging value(s)
     *
     * @var array
     */
    protected $_initialParamValues = array();

    /**
     * Last used paging value(s)
     *
     * @var array
     */
    protected $_lastParamValues = array();

    /**
     * Next paging value(s) to be used
     *
     * @var array
     */
    protected $_nextParamValues = array();

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(Bronto_Api_Rowset $rowset)
    {
        $this->_apiObject = $rowset->getApiObject();

        if (!$this->_apiObject->canIterate()) {
            throw new Bronto_Api_Rowset_Exception(sprintf('Cannot iterate results for %s', $this->_apiObject->getName()));
        }

        $this->_type   = $this->_apiObject->getIteratorType();
        $this->_params = $this->_apiObject->getIteratorParams();
        $this->_rowset = $rowset;
        $this->_count  = $rowset->count();

        // Set initial/next values
        $this->_setupParamValues();
    }

    /**
     * @return Bronto_Api_Object
     */
    public function getApiObject()
    {
        return $this->_apiObject;
    }

    protected function _setupParamValues()
    {
        $params = $this->_rowset->getParams();
        $this->_lastParamValues = $params;

        if (empty($this->_initialParamValues)) {
            $this->_initialParamValues = $params;
        }

        // Loop through each field we have to check/update
        foreach ($this->_params as $queryParam => $rowField) {
            // Loop through each initial API query params
            foreach ($params as $key => $value) {
                if (!is_array($value)) {
                    $value = array($value);
                }
                foreach ($value as $subkey => $subvalue) {
                    if ($subkey == $queryParam) {
                        switch ($queryParam) {
                            case 'readDirection':
                                $this->_nextParamValues[$queryParam] = Bronto_Api_Object::DIRECTION_NEXT;
                                break;
                            case 'pageNumber':
                                $this->_nextParamValues[$queryParam] = $subvalue + 1;
                                break;
                            default:
                                $this->_nextParamValues[$queryParam] = $subvalue;
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array
     */
    protected function _getNextParamValues(array $skipParams = array())
    {
        $params = $this->_lastParamValues;

        // Loop through each field we have to update
        foreach ($this->_params as $queryParam => $rowField) {
            if (in_array($queryParam, $skipParams)) {
                continue;
            }
            // Loop through each API query param
            foreach ($params as $key => $value) {
                if (!is_array($value)) {
                    if ($key == $queryParam) {
                        $params[$key] = $this->_nextParamValues[$queryParam];
                    }
                } else {
                    foreach ($value as $subkey => $subvalue) {
                        if ($subkey == $queryParam) {
                            $params[$key][$subkey] = $this->_nextParamValues[$queryParam];
                        }
                    }
                }
            }
        }

        return $params;
    }

    /**
     * @param int $pad
     * @return int|string
     */
    public function getCurrentPage($pad = false)
    {
        if ($pad !== false) {
            $pad = (int) $pad;
            return sprintf("%0{$pad}d", $this->_page);
        }

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
     * @return Bronto_Api_Row current element from the collection
     */
    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }

        /* @var $row Bronto_Api_Row */
        $row = $this->_rowset->current();

        // Loop through each field we have to update
        foreach ($this->_params as $queryParam => $rowField) {
            // Skip fields that are query only
            if (!$rowField) {
                continue;
            }
            if (isset($row->{$rowField}) && !empty($row->{$rowField})) {
                $this->_nextParamValues[$queryParam] = $row->{$rowField};
                if ($row->isDateField($rowField)) {
                    $this->_nextParamValues[$queryParam] = date('c', strtotime($row->{$rowField}));
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
        unset($this->_rowset);

        // Skip some fields under certain circumstances
        $skipParams = array();
        if ($this->_type == self::TYPE_STREAM) {
            $skipParams = $this->_params;
            unset($skipParams['readDirection']);
        }

        // Make request for the new rowset
        try {
            $params = $this->_getNextParamValues(array_keys($skipParams));
            $this->_rowset = $this->_apiObject->read($params);
        } catch (Exception $e) {
            if ($this->_type == self::TYPE_STREAM) {
                // Reset readDirection
                $this->_nextParamValues['readDirection'] = Bronto_Api_Object::DIRECTION_FIRST;
                // Get params again without skipping
                $params = $this->_getNextParamValues();
                $this->_rowset = $this->_apiObject->read($params);
            } else {
                throw $e;
            }
        }

        if (!$this->_rowset) {
            throw new Bronto_Api_Rowset_Exception('Retrieving the next Rowset failed');
        }

        // Increments
        $this->_newPage = true;
        $this->_page    = $this->_page + 1;
        $this->_count   = $this->_count + $this->_rowset->count();

        // Re-setup params
        $this->_setupParamValues();

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
     * @return array
     */
    public function getLastParamValues()
    {
        return $this->_lastParamValues;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function getLastParamValue($param)
    {
        if (isset($this->_lastParamValues[$param])) {
            return $this->_lastParamValues[$param];
        }

        foreach ($this->_lastParamValues as $key => $value) {
            if (is_array($value)) {
                if (isset($value[$param])) {
                    return $value[$param];
                }
            }
        }

        return false;
    }

    /**
     * @return Bronto_Api
     */
    public function getApi()
    {
        return $this->_apiObject->getApi();
    }

    /**
     * @return string
     */
    public function getLastRequest()
    {
        return $this->_apiObject->getApi()->getLastRequest();
    }

    /**
     * @return string
     */
    public function getLastResponse()
    {
        return $this->_apiObject->getApi()->getLastResponse();
    }
}
