<?php

abstract class Bronto_Api_Abstract
{
    /**
     * Bronto_Api object
     *
     * @var Bronto_Api
     */
    protected $_api;

    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = null;

    /**
     * The object name (for reading).
     *
     * @var string
     */
    protected $_nameRead = null;

    /**
     * The object name (for adding).
     *
     * @var string
     */
    protected $_nameAdd = null;

    /**
     * The object name (for updating).
     *
     * @var string
     */
    protected $_nameUpdate = null;

    /**
     * The object name (for deleting).
     *
     * @var string
     */
    protected $_nameDelete = null;

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key
     * as a string.
     *
     * @var mixed
     */
    protected $_primary = null;

    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Row';

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass = 'Bronto_Api_Rowset';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Exception';

    /**
     * Constructor
     *
     * @param  mixed $config
     * @return void
     */
    public function __construct($config = array())
    {
        if (isset($config['api']) && $config['api'] instanceof Bronto_Api) {
            $this->_api = $config['api'];
        }

        if (empty($this->_nameAdd)) {
            $this->_nameAdd = $this->_name;
        }

        if (empty($this->_nameRead)) {
            $this->_nameRead = $this->_name;
        }

        if (empty($this->_nameUpdate)) {
            $this->_nameUpdate = $this->_name;
        }

        if (empty($this->_nameDelete)) {
            $this->_nameDelete = $this->_name;
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
     * @param array $data
     * @return Bronto_Api_Row_Abstract
     */
    public function createRow(array $data = array())
    {
        $config = array(
            'apiObject' => $this,
            'data'      => $data,
            'readOnly'  => false,
            'stored'    => false
        );

        $rowClass = $this->getRowClass();


        if (!class_exists($rowClass)) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("Cannot find Row class: {$rowClass}");
        }
        $row = new $rowClass($config);
        $row->setFromArray($data);
        return $row;
    }

    /**
     * @param array $data
     * @return array
     */
    public function add(array $data = array())
    {
        if (isset($data[0])) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass('add() only allows adding one item at a time.');
        }

        $client   = $this->getApi()->getSoapClient();
        $function = "add{$this->_nameAdd}";
        $result   = $client->$function(array($data))->return;
        $row      = array_shift($result->results);

        if (isset($result->errors) && $result->errors) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass($row->errorString, $row->errorCode);
        }

        return array('id' => $row->id);
    }

    /**
     * @param array $data
     * @return array
     */
    public function update(array $data = array())
    {
        if (isset($data[0])) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass('update() only allows updating one item at a time.');
        }

        $client   = $this->getApi()->getSoapClient();
        $function = "update{$this->_nameUpdate}";
        $result   = $client->$function(array($data))->return;
        $row      = array_shift($result->results);

        if (isset($result->errors) && $result->errors) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass($row->errorString, $row->errorCode);
        }

        return array('id' => $row->id);
    }

    /**
     * @param array $params
     * @return Bronto_Api_Row_Abstract
     */
    public function readAll(array $params = array())
    {
        if (!is_array($params)) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass('You must pass an array to readAll()');
        }

        $client   = $this->getApi()->getSoapClient();
        $function = "read{$this->_nameRead}";
        $result   = $client->$function($params);

        if (!isset($result->return)) {
            $result->return = array();
        }

        $config = array(
            'apiObject' => $this,
            'data'      => $result->return,
            'rowClass'  => $this->getRowClass(),
            'stored'    => true,
        );

        $rowsetClass = $this->getRowsetClass();
        if (!class_exists($rowsetClass)) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("Cannot find Rowset class: {$rowsetClass}");
        }
        return new $rowsetClass($config);
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function delete(array $data = array())
    {
        $client   = $this->getApi()->getSoapClient();
        $function = "delete{$this->_nameDelete}";
        $result   = $client->$function(array($data))->return;
        $row      = array_shift($result->results);

        if (isset($result->errors) && $result->errors) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass($row->errorString, $row->errorCode);
        }

        return true;
    }

    /**
     * @param Bronto_Api $api
     * @return Bronto_Api_Abstract
     */
    public function setApi(Bronto_Api $api)
    {
        $this->_api = $api;
        return $this;
    }

    /**
     * @return Bronto_Api
     */
    public function getApi()
    {
        return $this->_api;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string
     */
    public function getRowClass()
    {
        return $this->_rowClass;
    }

    /**
     * @return string
     */
    public function getRowsetClass()
    {
        return $this->_rowsetClass;
    }

    /**
     * @return string
     */
    public function getExceptionClass()
    {
        return $this->_exceptionClass;
    }
}