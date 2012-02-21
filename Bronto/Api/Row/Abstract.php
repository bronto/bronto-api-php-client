<?php

abstract class Bronto_Api_Row_Abstract implements ArrayAccess, IteratorAggregate
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
     * a database, specified as a new tuple in the constructor, or
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
     * A row is marked read only if it contains columns that are not physically represented within
     * the database schema (e.g. evaluated columns/Zend_Db_Expr columns). This can also be passed
     * as a run-time config options as a means of protecting row data.
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
     * @var Bronto_Api_Abstract
     */
    protected $_apiObject;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct(array $config = array())
    {
        if (isset($config['apiObject']) && $config['apiObject'] instanceof Bronto_Api_Abstract) {
            $this->_apiObject = $config['apiObject'];
        }

        if (isset($config['data'])) {
            if (!is_array($config['data'])) {
                require_once 'Bronto/Api/Row/Exception.php';
                throw new Bronto_Api_Row_Exception('Data must be an array');
            }
            $this->_data = $config['data'];
        }
        if (isset($config['stored']) && $config['stored'] === true) {
            $this->_cleanData = $this->_data;
        }

        if (isset($config['readOnly']) && $config['readOnly'] === true) {
            $this->_readOnly = true;
        }

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
     * Sets all data in the row from an array.
     *
     * @param  array $data
     * @return Zend_Db_Table_Row_Abstract Provides a fluent interface
     */
    public function setFromArray(array $data)
    {
        $data = array_intersect_key($data, $this->_data);

        foreach ($data as $columnName => $value) {
            $this->__set($columnName, $value);
        }

        return $this;
    }

    /**
     * Refreshes properties from the API.
     *
     * @return void
     */
    protected function _refresh($pull = true)
    {
        if ($pull) {
            $this->_data = $this->read(true);
        }
        $this->_cleanData = $this->_data;
        $this->_modifiedFields = array();
    }

    /**
     * Allows pre-add logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _preAdd()
    {
    }

    /**
     * Allows post-add logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _postAdd()
    {
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _preUpdate()
    {
    }

    /**
     * Allows post-update logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _postUpdate()
    {
    }

    /**
     * Allows pre-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _preDelete()
    {
    }

    /**
     * Allows post-delete logic to be applied to row.
     * Subclasses may override this method.
     *
     * @return void
     */
    protected function _postDelete()
    {
    }

    /**
     * Saves the properties to the API.
     *
     * This performs an intelligent add/update, and reloads the
     * properties with fresh data from the table on success.
     *
     * @param bool $upsert
     * @param bool $refresh
     * @return array
     */
    public function save($upsert = true, $refresh = true)
    {
        if (!$this->getApiObject()->hasUpsert()) {
            $upsert = false;
        }

        if ($upsert) {
            return $this->_add(true, $refresh);
        } else {
            /**
             * If the _cleanData array is empty,
             * this is an ADD of a new row.
             * Otherwise it is an UPDATE.
             */
            if (empty($this->_cleanData)) {
                return $this->_add(false, $refresh);
            } else {
                return $this->_update($refresh);
            }
        }
    }

    /**
     * @param array $filter
     * @param bool $returnData
     * @return Bronto_Api_Row_Abstract
     */
    protected function _read(array $filter = array(), $returnData = false)
    {
        $rowset = $this->getApiObject()->readAll($filter);

        if ($rowset->count() == 0) {
            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass('An empty result was returned', Bronto_Api_Exception::EMPTY_RESULT);
        }

        $data = $rowset->current()->getData();
        if ($returnData) {
            return $data;
        }

        $this->_data           = $data;
        $this->_cleanData      = $this->_data;
        $this->_modifiedFields = array();
        return $this;
    }

    protected function _add($upsert = false, $refresh = true)
    {
        /**
         * A read-only row cannot be saved.
         */
        if ($this->_readOnly === true) {
            require_once 'Bronto/Api/Row/Exception.php';
            throw new Bronto_Api_Row_Exception('This row has been marked read-only');
        }

        /**
         * Run pre-ADD logic
         */
        $this->_preAdd();

        /**
         * Execute the ADD (this may throw an exception)
         */
        $data = array_intersect_key($this->_data, $this->_modifiedFields);
        if ($upsert) {
            $primaryKey = $this->getApiObject()->addOrUpdate($data);
        } else {
            $primaryKey = $this->getApiObject()->add($data);
        }

        /**
         * Normalize the result to an array indexed by primary key column(s).
         * The table add() method may return a scalar.
         */

        if (is_array($primaryKey)) {
            $newPrimaryKey = $primaryKey;
        } else {
            $tempPrimaryKey = (array) $this->_primary;
            $newPrimaryKey  = array(current($tempPrimaryKey) => $primaryKey);
        }

        /**
         * Save the new primary key value in _data.  The primary key may have
         * been generated by a sequence or auto-increment mechanism, and this
         * merge should be done before the _postAdd() method is run, so the
         * new values are available for logging, etc.
         */
        $this->_data = array_merge($this->_data, $newPrimaryKey);

        /**
         * Run post-ADD logic
         */
        $this->_postAdd();

        /**
         * Update the _cleanData to reflect that the data has been inserted.
         */
        $noRefresh = $this->getApiObject()->getApi()->getOption('noRefresh');
        if ($noRefresh == true || $refresh == false) {
            $this->_refresh(false);
        } else {
            $this->_refresh();
        }

        return $primaryKey;
    }

    protected function _update($refresh = true)
    {
        /**
         * A read-only row cannot be saved.
         */
        if ($this->_readOnly === true) {
            require_once 'Bronto/Api/Row/Exception.php';
            throw new Bronto_Api_Row_Exception('This row has been marked read-only');
        }

        /**
         * Run pre-UPDATE logic
         */
        $this->_preUpdate();

        /**
         * Compare the data to the modified fields array to discover
         * which columns have been changed.
         */
        $diffData = array_intersect_key($this->_data, $this->_modifiedFields);

        /**
         * Execute the UPDATE (this may throw an exception)
         * Do this only if data values were changed.
         * Use the $diffData variable, so the UPDATE statement
         * includes SET terms only for data values that changed.
         */
        if (count($diffData) > 0) {
            $tempPrimaryKey = $this->_primary;
            $primaryKey = $this->getApiObject()->update(array_merge(array($this->_primary => $this->{$tempPrimaryKey}), $diffData));
        } else {
            $tempPrimaryKey = $this->_primary;
            $primaryKey = $this->{$tempPrimaryKey};
        }

        /**
         * Run post-UPDATE logic.  Do this before the _refresh()
         * so the _postUpdate() function can tell the difference
         * between changed data and clean (pre-changed) data.
         */
        $this->_postUpdate();

        /**
         * Refresh the data just in case triggers in the API changed
         * any columns.  Also this resets the _cleanData.
         */
        $noRefresh = $this->getApiObject()->getApi()->getOption('noRefresh');
        if ($noRefresh == true || $refresh == false) {
            $this->_refresh(false);
        } else {
            $this->_refresh();
        }

        return $primaryKey;
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function _delete(array $data = array())
    {
        /**
         * A read-only row cannot be saved.
         */
        if ($this->_readOnly === true) {
            require_once 'Bronto/Api/Row/Exception.php';
            throw new Bronto_Api_Row_Exception('This row has been marked read-only');
        }

        /**
         * Execute pre-DELETE logic
         */
        $this->_preDelete();

        /**
         * Execute the DELETE (this may throw an exception)
         */
        $result = $this->getApiObject()->delete($data);

        /**
         * Execute post-DELETE logic
         */
        $this->_postDelete();

        /**
         * Reset all fields to null to indicate that the row is not there
         */
        $this->_data = array_combine(
            array_keys($this->_data),
            array_fill(0, count($this->_data), null)
        );

        return $result;
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
     * @return void
     */
    public function __set($columnName, $value)
    {
        $this->_data[$columnName] = $value;
        $this->_modifiedFields[$columnName] = true;
    }

    /**
     * Unset row field value
     *
     * @param  string $columnName The column key.
     * @return Bronto_Api_Row_Abstract
     */
    public function __unset($columnName)
    {
        unset($this->_data[$columnName]);
        return $this;
    }

    /**
     * Test existence of row field
     *
     * @param  string  $columnName   The column key.
     * @return boolean
     */
    public function __isset($columnName)
    {
        return array_key_exists($columnName, $this->_data);
    }

    /**
     * @param Bronto_Api_Abstract $apiObject
     * @return Bronto_Api_Row_Abstract
     */
    public function setApiObject(Bronto_Api_Abstract $apiObject)
    {
        $this->_apiObject = $apiObject;
        return $this;
    }

    /**
     * @return Bronto_Api_Abstract
     */
    public function getApiObject()
    {
        return $this->_apiObject;
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
     * @param array $data
     * @return Bronto_Api_Row_Abstract
     */
    public function setData(array $data = array())
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }
}