<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Rowset implements SeekableIterator, Countable, ArrayAccess
{
    /**
     * The original data for each row.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Any errors for each row.
     *
     * @var array
     */
    protected $_errors = false;

    /**
     * API Object
     *
     * @var Bronto_Api_Object
     */
    protected $_apiObject;

    /**
     * API Object class name
     *
     * @var string
     */
    protected $_apiObjectClass;

    /**
     * Row class name
     *
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Row';

    /**
     * Iterator pointer.
     *
     * @var integer
     */
    protected $_pointer = 0;

    /**
     * How many data rows there are.
     *
     * @var integer
     */
    protected $_count;

    /**
     * Collection of instantiated Bronto_Api_Row objects.
     *
     * @var array
     */
    protected $_rows = array();

    /**
     * @var boolean
     */
    protected $_stored = false;

    /**
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * @var array
     */
    protected $_params = array();

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['apiObject'])) {
            $this->_apiObject      = $config['apiObject'];
            $this->_apiObjectClass = get_class($this->_apiObject);
        }

        if (isset($config['rowClass'])) {
            $this->_rowClass = (string) $config['rowClass'];
        }

        if (isset($config['data'])) {
            $this->_data = (array) $config['data'];
            foreach ($this->_data as $key => $value) {
                $this->_data[$key] = (array) $value;
            }
        }

        if (isset($config['errors'])) {
            $this->_errors = (array) $config['errors'];
        }

        if (isset($config['readOnly'])) {
            $this->_readOnly = (bool) $config['readOnly'];
        }

        if (isset($config['stored'])) {
            $this->_stored = (bool) $config['stored'];
        }

        if (isset($config['params'])) {
            $this->_params = $config['params'];
        }

        // Set the count of rows
        $this->_count = count($this->_data);
        $this->init();
    }

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * @return Bronto_Api_Object
     */
    public function getApiObject()
    {
        return $this->_apiObject;
    }

    /**
     * @return string
     */
    public function getApiObjectClass()
    {
        return $this->_apiObjectClass;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Rewind the Iterator to the first element.
     * Similar to the reset() function for arrays in PHP.
     * Required by interface Iterator.
     *
     * @return Bronto_Api_Rowset Fluent interface.
     */
    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
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

        // Do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $this->_rows[$this->_pointer] = new $this->_rowClass(
                array(
                    'apiObject' => $this->_apiObject,
                    'data'      => $this->_data[$this->_pointer],
                    'stored'    => $this->_stored,
                    'readOnly'  => $this->_readOnly
                )
            );
        }

        // return the row object
        return $this->_rows[$this->_pointer];
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
        return $this->_pointer;
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
        ++$this->_pointer;
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
        return $this->_pointer >= 0 && $this->_pointer < $this->_count;
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
     * Take the Iterator to position $position
     * Required by interface SeekableIterator.
     *
     * @param int $position the position to seek to
     * @return Bronto_Api_Rowset
     */
    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position >= $this->_count) {
            throw new Bronto_Api_Rowset_Exception("Illegal index {$position}");
        }
        $this->_pointer = $position;
        return $this;
    }

    /**
     * Check if an offset exists
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[(int) $offset]);
    }

    /**
     * Get the data for the given offset
     *
     * @param string $offset
     * @return array
     */
    public function offsetGetData($offset)
    {
        $offset = (int) $offset;
        if ($offset < 0 || $offset >= $this->_count) {
            throw new Bronto_Api_Rowset_Exception("Illegal index {$offset}");
        }

        return $this->_data[$offset];
    }

    /**
     * Get the row for the given offset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return Bronto_Api_Row
     */
    public function offsetGet($offset)
    {
        $offset = (int) $offset;
        if ($offset < 0 || $offset >= $this->_count) {
            throw new Bronto_Api_Rowset_Exception("Illegal index {$offset}");
        }
        $this->_pointer = $offset;

        return $this->current();
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Does nothing
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Seamlessly iterate over this rowset
     *
     * @return Bronto_Api_Rowset_Iterator
     */
    public function iterate()
    {
        return new Bronto_Api_Rowset_Iterator($this);
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->_errors);
    }

    /**
     * @return bool|array
     */
    public function getErrors()
    {
        if ($this->hasErrors()) {
            $errors = array();
            foreach ($this->_errors as $pointer) {
                if ($this->offsetExists($pointer)) {
                    $row = $this->offsetGet($pointer);
                    $errors[] = array(
                        'code'    => $row->getErrorCode(),
                        'message' => $row->getErrorMessage(),
                    );
                }
            }
            return $errors;
        }
        return false;
    }

    /**
     * @return bool|array
     */
    public function getError()
    {
        if ($this->hasErrors()) {
            $error = array(
                'code'    => null,
                'message' => 'Unknown error',
            );
            foreach ($this->_errors as $pointer) {
                if ($this->offsetExists($pointer)) {
                    $row = $this->offsetGet($pointer);
                    $error = array(
                        'code'    => $row->getErrorCode(),
                        'message' => $row->getErrorMessage(),
                    );
                    break;
                }
            }
            return $error;
        }
        return false;
    }
}
