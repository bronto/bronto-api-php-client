<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 */
abstract class Bronto_Api_Object
{
    /**
     * Bronto_Api object
     *
     * @var Bronto_Api
     */
    protected $_api;

    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @var array
     */
    protected $_methods = array();

    /**
     * @var array
     */
    protected $_methodsByType = array();

    /**
     * The object name.
     *
     * @var string
     */
    protected $_name;

    /**
     * The primary key column or columns.
     * A compound key should be declared as an array.
     * You may declare a single-column primary key
     * as a string.
     *
     * @var mixed
     */
    protected $_primary;

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
     * @var int
     */
    protected $_iteratorType = Bronto_Api_Rowset_Iterator::TYPE_PAGE;

    /**
     * @var string
     */
    protected $_iteratorParam = 'pageNumber';

    /**
     * @var mixed
     */
    protected $_iteratorRowField;

    /**
     * @var bool
     */
    protected $_canIterate = true;

    /**
     * @var string
     */
    protected $_lastRequestMethod;

    /**
     * @var array
     */
    protected $_lastRequestData;

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

        foreach ($this->_methods as $method => $type) {
            if (is_string($type)) {
                $this->_methodsByType[$type] = $method;
            }
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
     * @return Bronto_Api_Row
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

        return new $rowClass($config);
    }

    /**
     * @param array $data
     * @return Bronto_Api_Rowset
     */
    public function add(array $data = array())
    {
        $method = $this->_methodsByType['add'];
        if (array_values($data) !== $data) {
            $data = array($data);
        }
        return $this->doRequest($method, $data, true);
    }

    /**
     * @param array $data
     * @return Bronto_Api_Rowset
     */
    public function update(array $data = array())
    {
        $method = $this->_methodsByType['update'];
        if (array_values($data) !== $data) {
            $data = array($data);
        }
        return $this->doRequest($method, $data, true);
    }

    /**
     * @param array $data
     * @return Bronto_Api_Rowset
     */
    public function addOrUpdate(array $data = array())
    {
        $method = $this->_methodsByType['addOrUpdate'];
        if (array_values($data) !== $data) {
            $data = array($data);
        }
        return $this->doRequest($method, $data, true);
    }

    /**
     * @param array $data
     * @return Bronto_Api_Rowset
     */
    public function delete(array $data = array())
    {
        $method = $this->_methodsByType['delete'];
        if (array_values($data) !== $data) {
            $data = array($data);
        }
        return $this->doRequest($method, $data, true);
    }

    /**
     * @param array $data
     * @return Bronto_Api_Rowset
     */
    public function read(array $data = array())
    {
        $method = $this->_methodsByType['read'];
        return $this->doRequest($method, $data, false);
    }

    /**
     * @param string $method
     * @param array $data
     */
    protected function _beforeRequest($method, array $data = array())
    {
        if (!isset($this->_methods[$method])) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("Method '{$method}' not allowed on " . $this->getName());
        }
    }

    /**
     * @param string $method
     * @param array $data
     * @param bool $canUseRetryer
     * @return Bronto_Api_Rowset
     */
    public function doRequest($method, array $data, $canUseRetryer = false)
    {
        $this->_beforeRequest($method, $data);

        $maxTries = (int) $this->getApi()->getOption('retry_limit', 5);
        $tries    = 0;
        $success  = false;

        // Handle [frequent] API failures
        do {
            $tries++;
            $error = false;

            try {
                // Store this request in case we need to retry later
                $this->_lastRequestMethod = $method;
                $this->_lastRequestData   = $data;

                // Attempt
                $client = $this->getApi()->getSoapClient();
                $result = $client->$method($data);
            } catch (Exception $e) {
                $error          = true;
                $exceptionClass = $this->getExceptionClass();
                $exception      = new $exceptionClass($e->getMessage(), $e->getCode(), $tries, $e);
                if (!$exception->isRecoverable() || $tries === $maxTries) {
                    if ($canUseRetryer && $exception->isRecoverable()) {
                        if ($retryer = $this->getApi()->getRetryer(array('path' => TESTS_TEMP))) {
                            $retryer->store($this);
                        }
                    }
                    return $this->getApi()->throwException($exception);
                } else {
                    // Attempt to get a new session token
                    sleep(5);
                    $this->getApi()->login();
                }
            }

            if (!$error) {
                $success = true;
            }

        } while (!$success && $tries <= $maxTries);

        $result = isset($result->return) ? (array) $result->return : array();

        return $this->_parseResponse($result);
    }

    /**
     * @param array $result
     * @return Bronto_Api_Rowset
     */
    public function _parseResponse(array $result)
    {
        $data = array();
        if (isset($result['results'])) {
            $data = (array) $result['results'];
        } else {
            if (isset($result[0])) {
                $data = $result;
            }
        }

        $config = array(
            'apiObject' => $this,
            'rowClass'  => $this->getRowClass(),
            'data'      => $data,
            'errors'    => isset($result['errors']) ? (array) $result['errors'] : array(),
            'stored'    => true,
            'params'    => $data,
        );

        $rowsetClass = $this->getRowsetClass();
        if (!class_exists($rowsetClass)) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("Cannot find Rowset class: {$rowsetClass}");
        }

        return new $rowsetClass($config);
    }

    /**
     * @param Bronto_Api $api
     * @return Bronto_Api_Object
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
        if ($this->_name === null) {
            $className = get_class($this);
            $this->_name = str_replace('Bronto_Api_', '', $className);
        }

        return $this->_name;
    }

    /**
     * @return string
     */
    public function getRowClass()
    {
        if ($this->_rowClass === 'Bronto_Api_Row') {
            $className = get_class($this);
            $rowClass = "{$className}_Row";
            if (class_exists($rowClass)) {
                $this->_rowClass = $rowClass;
            }
        }

        return $this->_rowClass;
    }

    /**
     * @return string
     */
    public function getRowsetClass()
    {
        if ($this->_rowsetClass === 'Bronto_Api_Rowset') {
            $className = get_class($this);
            $rowsetClass = "{$className}_Rowset";
            if (class_exists($rowsetClass)) {
                $this->_rowsetClass = $rowsetClass;
            }
        }

        return $this->_rowsetClass;
    }

    /**
     * @return string
     */
    public function getExceptionClass()
    {
        if ($this->_exceptionClass === 'Bronto_Api_Exception') {
            $className = get_class($this);
            $exceptionClass = "{$className}_Exception";
            if (class_exists($exceptionClass)) {
                $this->_exceptionClass = $exceptionClass;
            }
        }

        return $this->_exceptionClass;
    }

    /**
     * @param string $key
     * @return array|boolean
     */
    public function getOptionValues($key)
    {
        if (isset($this->_options[$key])) {
            return $this->_options[$key];
        }

        return false;
    }

    /**
     * @param string $key
     * @param string $value
     * @return boolean
     */
    public function isValidOptionValue($key, $value)
    {
        if ($values = $this->getOptionValues($key)) {
            return in_array($value, $values);
        }

        return true;
    }

    /**
     * @return int
     */
    public function getIteratorType()
    {
        return $this->_iteratorType;
    }

    /**
     * @return string
     */
    public function getIteratorParam()
    {
        return $this->_iteratorParam;
    }

    /**
     * @return string|bool
     */
    public function getIteratorRowField()
    {
        return !empty($this->_iteratorRowField) ? $this->_iteratorRowField : false;
    }

    /**
     * @return bool
     */
    public function canIterate()
    {
        return (bool) $this->_canIterate;
    }

    /**
     * @param string $method
     * @return bool
     */
    public function hasMethod($method)
    {
        return isset($this->_methods[$method]);
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasMethodType($type)
    {
        return isset($this->_methodsByType[$type]);
    }

    /**
     * @return string
     */
    public function getLastRequestMethod()
    {
        return $this->_lastRequestMethod;
    }

    /**
     * @return array
     */
    public function getLastRequestData()
    {
        return $this->_lastRequestData;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array(
            '_api',
            '_lastRequestMethod',
            '_lastRequestData',
        );
    }
}