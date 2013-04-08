<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
abstract class Bronto_Api_Row implements ArrayAccess, IteratorAggregate
{
    /**
     * The data for each column in the row (column_name => value).
     * The keys must match the physical names of columns in the
     * table for which this row is defined.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * This is set to a copy of $_data when the data is fetched from
     * the API, specified as a new tuple in the constructor, or
     * when dirty data is posted to the database with save().
     *
     * @var array
     */
    protected $_cleanData = array();

    /**
     * Tracks columns where data has been updated. Allows more specific insert and
     * update operations.
     *
     * @var array
     */
    protected $_modifiedFields = array();

    /**
     * Tracks columns that are dates.
     *
     * @var array
     */
    protected $_dateFields = array();

    /**
     * A row is marked read only if it contains columns that are not physically
     * represented within the API schema. This can also be passed as a
     * run-time config options as a means of protecting row data.
     *
     * @var boolean
     */
    protected $_readOnly = false;

    /**
     * Primary row key
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * API Object
     *
     * @var Bronto_Api_Object
     */
    protected $_apiObject;

    /**
     * @var bool
     */
    protected $_isNew = false;

    /**
     * @var bool
     */
    protected $_isError = false;

    /**
     * @var int
     */
    protected $_errorCode;

    /**
     * @var string
     */
    protected $_errorString;

    /**
     * @var bool
     */
    protected $_isLoaded = true;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['apiObject']) && $config['apiObject'] instanceof Bronto_Api_Object) {
            $this->_apiObject = $config['apiObject'];
        }

        if (isset($config['data'])) {
            if (!is_array($config['data'])) {
                throw new Bronto_Api_Row_Exception('Data must be an array');
            }
            $this->setData($config['data']);
        }

        if (isset($config['stored']) && $config['stored'] === true) {
            $this->_cleanData = $this->_data;
        } else {
            $this->_isLoaded       = false;
            $this->_cleanData      = array();
            foreach ($this->_data as $key => $value) {
                $this->_modifiedFields[$key] = true;
            }
        }

        if (isset($config['readOnly']) && $config['readOnly'] === true) {
            $this->_readOnly = true;
        }

        $this->init();
    }

    /**
     * @param array $data
     * @return Bronto_Api_Row
     */
    public function setData(array $data = array())
    {
        if (isset($data['isNew'])) {
            $this->_isNew    = (bool) $data['isNew'];
            $this->_isLoaded = true;
            unset($data['isNew']);
        }

        if (isset($data['isError'])) {
            $this->_isError  = (bool) $data['isError'];
            if ($this->_isError) {
                $this->_readOnly = true;
                $this->_isLoaded = false;
            }
            unset($data['isError']);
        }

        if (isset($data['errorCode'])) {
            $this->_errorCode = (int) $data['errorCode'];
            unset($data['errorCode']);
        }

        if (isset($data['errorString'])) {
            $this->_errorString = (string) $data['errorString'];
            unset($data['errorString']);
        }

        $this->_data = array_merge($this->_data, $data);
        $this->_refresh(false);
        $this->init();

        return $this;
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
     * Proxy to __isset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * Proxy to __get
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @return string
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Proxy to __set
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * Proxy to __unset
     * Required by the ArrayAccess implementation
     *
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        return $this->__unset($offset);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator((array) $this->_data);
    }

    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array) $this->_data;
    }

    /**
     * @return array
     */
    public function __toArray()
    {
        return $this->toArray();
    }

    /**
     * @return Bronto_Api_Row
     */
    public function persist()
    {
        if ($this->_readOnly === true) {
            throw new Bronto_Api_Row_Exception(sprintf("Cannot persist a %s record.", $this->getApiObject()->getName()));
        }

        $type = false;
        if ($this->getApiObject()->hasMethodType('addOrUpdate')) {
            $type = 'addOrUpdate';
        } else {
            if (empty($this->_cleanData)) {
                $type = 'add';
            } else {
                $type = 'update';
            }
        }

        return $this->_persist($type);
    }

    /**
     * @return Bronto_Api_Row
     */
    public function persistDelete()
    {
        if (!$this->getApiObject()->hasMethodType('delete')) {
            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass('Cannot delete a row of type: ' . $this->getApiObject()->getName());
        }

        return $this->_persist('delete');
    }

    /**
     * Persist an object for write caching
     *
     * @param string $type
     * @param mixed $defaultIndex
     * @return Bronto_Api_Row
     */
    public function _persist($type, $defaultIndex = false)
    {
        $data = array_intersect_key($this->_data, $this->_modifiedFields);
        $tempPrimaryKey = $this->_primary;
        if (!empty($this->{$tempPrimaryKey})) {
            $defaultIndex = $this->{$tempPrimaryKey};
            if ($type === 'delete') {
                $data = array($this->_primary => $this->{$tempPrimaryKey});
            } else {
                $data = array_merge(array($this->_primary => $this->{$tempPrimaryKey}), $data);
            }
        }

        $this->getApiObject()->addToWriteCache($type, $data, $defaultIndex);
        return $this;
    }

    /**
     * @return Bronto_Api_Row
     */
    public function read()
    {
        $data = array();
        if ($this->id) {
            $data = array('id' => $this->id);
        } else {
            throw new Bronto_Api_Row_Exception('Trying to read Row without unique identifier for lookup');
        }

        $this->_read($data);
        return $this;
    }

    /**
     * @param bool $upsert
     * @param bool $refresh
     * @return Bronto_Api_Row
     */
    public function save($upsert = false, $refresh = false)
    {
        $this->_save($upsert, $refresh);
        return $this;
    }

    /**
     * @return Bronto_Api_Row
     */
    public function delete()
    {
        if (!$this->getApiObject()->hasMethodType('delete')) {
            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass('Cannot delete a row of type: ' . $this->getApiObject()->getName());
        }

        $data = array();
        if (!$this->id) {
            $this->_refresh();
        }

        if ($this->id) {
            $data = array('id' => $this->id);
        } else {
            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass('Trying to delete Row without unique identifier for lookup');
        }

        $this->_delete($data);
        return $this;
    }

    /**
     * Refreshes properties from the API.
     * @param bool $pull
     */
    protected function _refresh($pull = true)
    {
        if ($pull) {
            $this->read();
        }
        $this->_cleanData = $this->_data;
        $this->_modifiedFields = array();
    }

    /**
     * @param array $filter
     */
    protected function _read(array $filter = array())
    {
        if (empty($filter)) {
            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass('Trying to read Row without unique identifier for lookup');
        }

        /* @var $rowset Bronto_Api_Rowset */
        $rowset = $this->getApiObject()->readAll($filter);

        if ($rowset->hasErrors()) {
            // Reset class
            $error = $rowset->getError();
            $this->_readOnly    = true;
            $this->_isLoaded    = false;
            $this->_isError     = true;
            $this->_errorCode   = $error['code'];
            $this->_errorString = $error['message'];

            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass($error['message'], $error['code']);
        }

        if ($rowset->count() > 0) {
            // Reset all fields
            $this->_isLoaded = true;
            $this->_readOnly = false;
            $this->_isError  = false;
            $this->_isNew    = false;

            $data = $rowset->offsetGetData(0);
            $this->setData($data);
        }
    }

    /**
     * Saves the properties to the API.
     *
     * This performs an intelligent add/update, and can reload the
     * properties with fresh data from the API on success.
     *
     * @param bool $upsert
     * @param bool $refresh
     */
    protected function _save($upsert = true, $refresh = false)
    {
        /**
         * If the _cleanData array is empty,
         * this is an ADD of a new row.
         * Otherwise it is an UPDATE.
         */
        if (empty($this->_cleanData)) {
            if ($upsert) {
                if ($this->getApiObject()->hasMethodType('addOrUpdate')) {
                    $this->_add(true);
                } else {
                    $this->_add(false);
                }
            } else {
                $this->_add(false);
            }
        } else {
            $this->_update();
        }

        $refreshOnSave = $this->getApi()->getOption('refresh_on_save');
        if ($refreshOnSave || $refresh) {
            $this->_refresh();
        }
    }

    /**
     * @param bool $upsert
     */
    protected function _add($upsert = false)
    {
        if ($this->_readOnly === true) {
            throw new Bronto_Api_Row_Exception(sprintf("Cannot create %s record.", $this->getApiObject()->getName()));
        }

        $data = array_intersect_key($this->_data, $this->_modifiedFields);
        if ($upsert) {
            $tempPrimaryKey = $this->_primary;
            if (!empty($this->{$tempPrimaryKey})) {
                $data = array_merge(array($this->_primary => $this->{$tempPrimaryKey}), $data);
            }
            $rowset = $this->getApiObject()->addOrUpdate(array($data));
        } else {
            $rowset = $this->getApiObject()->add(array($data));
        }

        if ($rowset->hasErrors()) {
            // Reset class
            $error = $rowset->getError();
            $this->_readOnly    = true;
            $this->_isLoaded    = false;
            $this->_isError     = true;
            $this->_errorCode   = $error['code'];
            $this->_errorString = $error['message'];

            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass($error['message'], $error['code']);
        }

        if ($rowset->count() > 0) {
            // Reset all fields
            $this->_isLoaded = true;
            $this->_readOnly = false;
            $this->_isError  = false;
            $this->_isNew    = false;

            $data = $rowset->offsetGetData(0);
            $this->setData($data);
        }
    }

    protected function _update()
    {
        if ($this->_readOnly === true) {
            throw new Bronto_Api_Row_Exception(sprintf("Cannot update %s record.", $this->getApiObject()->getName()));
        }

        $data = array_intersect_key($this->_data, $this->_modifiedFields);
        if (count($data) > 0) {
            $tempPrimaryKey = $this->_primary;
            if (!empty($this->{$tempPrimaryKey})) {
                $data = array_merge(array($this->_primary => $this->{$tempPrimaryKey}), $data);
            }
            $rowset = $this->getApiObject()->update(array($data));

            if ($rowset->hasErrors()) {
                // Reset class
                $error = $rowset->getError();
                $this->_readOnly    = true;
                $this->_isLoaded    = false;
                $this->_isError     = true;
                $this->_errorCode   = $error['code'];
                $this->_errorString = $error['message'];

                $exceptionClass = $this->getApiObject()->getExceptionClass();
                throw new $exceptionClass($error['message'], $error['code']);
            }

            if ($rowset->count() > 0) {
                // Reset all fields
                $this->_isLoaded = true;
                $this->_readOnly = false;
                $this->_isError  = false;
                $this->_isNew    = false;

                $data = $rowset->offsetGetData(0);
                $this->setData($data);
            }
        }
    }

    /**
     * @param array $data
     */
    protected function _delete(array $data)
    {
        if ($this->_readOnly === true) {
            throw new Bronto_Api_Row_Exception(sprintf("Cannot delete this read-only %s record.", $this->getApiObject()->getName()));
        }

        $rowset = $this->getApiObject()->delete(array($data));

        if ($rowset->hasErrors()) {
            // Reset class
            $error = $rowset->getError();
            $this->_readOnly    = true;
            $this->_isLoaded    = false;
            $this->_isError     = true;
            $this->_errorCode   = $error['code'];
            $this->_errorString = $error['message'];

            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass($error['message'], $error['code']);
        }

        if ($rowset->count() > 0) {
            // Reset all fields to indicate that the row is not there
            $this->_data           = array();
            $this->_cleanData      = array();
            $this->_modifiedFields = array();
            $this->_isLoaded       = false;
            $this->_readOnly       = true;
            $this->_isError        = false;
            $this->_isNew          = false;

            $data = $rowset->offsetGetData(0);
            $this->setData($data);
        }
    }

    /**
     * Retrieve row field value
     *
     * @param  string $columnName The user-specified column name.
     * @return string             The corresponding column value.
     */
    public function __get($columnName)
    {
        if (!array_key_exists($columnName, $this->_data)) {
            return null;
        }
        return $this->_data[$columnName];
    }

    /**
     * Set row field value
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     */
    public function __set($columnName, $value)
    {
        if ($this->_readOnly === false) {
            $this->_data[$columnName] = $value;
            $this->_modifiedFields[$columnName] = true;
        }
    }

    /**
     * Unset row field value
     *
     * @param  string $columnName The column key.
     */
    public function __unset($columnName)
    {
        if ($this->_readOnly === false) {
            unset($this->_data[$columnName]);
        }
    }

    /**
     * Test existence of row field
     *
     * @param  string  $columnName   The column key.
     * @return bool
     */
    public function __isset($columnName)
    {
        return array_key_exists($columnName, $this->_data);
    }

    /**
     * @param Bronto_Api_Object $apiObject
     * @return Bronto_Api_Row
     */
    public function setApiObject(Bronto_Api_Object $apiObject)
    {
        $this->_apiObject = $apiObject;
        return $this;
    }

    /**
     * @return Bronto_Api_Object
     */
    public function getApiObject()
    {
        return $this->_apiObject;
    }

    /**
     * @return Bronto_Api
     */
    public function getApi()
    {
        return $this->_apiObject->getApi();
    }

    /**
     * Test the read-only status of the row.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->_readOnly;
    }

    /**
     * Set the read-only status of the row.
     *
     * @param boolean $flag
     * @return boolean
     */
    public function setReadOnly($flag)
    {
        $this->_readOnly = (bool) $flag;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @return array
     */
    public function getDateFields()
    {
        return $this->_dateFields;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isDateField($key)
    {
        return (bool) isset($this->_dateFields[$key]);
    }

    /**
     * @return bool
     */
    public function isNew()
    {
        return (bool) $this->_isNew;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return (bool) $this->_isError;
    }

    /**
     * @return int|bool
     */
    public function getErrorCode()
    {
        if ($this->hasError()) {
            return $this->_errorCode;
        }
        return false;
    }

    /**
     * @return int|bool
     */
    public function getErrorMessage()
    {
        if ($this->hasError()) {
            return $this->_errorString;
        }
        return false;
    }
}
