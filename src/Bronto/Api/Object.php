<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 *
 * @method Bronto_Api_Rowset add() add(array $data)
 * @method Bronto_Api_Rowset addOrUpdate() addOrUpdate(array $data)
 * @method Bronto_Api_Rowset update() update(array $data)
 * @method Bronto_Api_Rowset delete() delete(array $data)
 * @method Bronto_Api_Rowset read() read(array $data)
 */
abstract class Bronto_Api_Object
{
    /** filterType */
    const TYPE_AND = 'AND';
    const TYPE_OR  = 'OR';

    /** filterOperator */
    const OPER_EQ              = 'EqualTo';
    const OPER_NE              = 'NotEqualTo';
    const OPER_GT              = 'GreaterThan';
    const OPER_LT              = 'LessThan';
    const OPER_GE              = 'GreaterThanEqualTo';
    const OPER_LE              = 'LessThanEqualTo';
    const OPER_CONTAINS        = 'Contains';
    const OPER_NOT_CONTAINS    = 'DoesNotContain';
    const OPER_STARTS_WITH     = 'StartsWith';
    const OPER_ENDS_WITH       = 'EndsWith';
    const OPER_NOT_STARTS_WITH = 'DoesNotStartWith';
    const OPER_NOT_ENDS_WITH   = 'DoesNotEndWith';
    const OPER_SAME_YEAR       = 'SameYear';
    const OPER_NOT_SAME_YEAR   = 'NotSameYear';
    const OPER_SAME_DAY        = 'SameDay';
    const OPER_NOT_SAME_DAY    = 'NotSameDay';
    const OPER_BEFORE          = 'Before';
    const OPER_AFTER           = 'After';
    const OPER_BEFORE_SAME     = 'BeforeOrSameDay';
    const OPER_AFTER_SAME      = 'AfterOrSameDay';

    /** readDirection */
    const DIRECTION_FIRST = 'FIRST';
    const DIRECTION_NEXT  = 'NEXT';

    /**
     * Bronto_Api object
     *
     * @var Bronto_Api
     */
    protected $_api;

    /**
     * Various options for this object
     *
     * @var array
     */
    protected $_options = array();

    /**
     * API Methods this object supports
     *
     * @var array
     */
    protected $_methods = array();

    /**
     * @var array
     */
    protected $_methodsByType = array();

    /**
     * The object name
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
    protected $_rowClass;

    /**
     * Classname for rowset
     *
     * @var string
     */
    protected $_rowsetClass;

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass;

    /**
     * @var string
     */
    protected $_defaultRowClass = 'Bronto_Api_Row';

    /**
     * @var string
     */
    protected $_defaultRowsetClass = 'Bronto_Api_Rowset';

    /**
     * @var string
     */
    protected $_defaultExceptionClass = 'Bronto_Api_Exception';

    /**
     * How to iterate this object (page/date)
     *
     * @var int
     */
    protected $_iteratorType = Bronto_Api_Rowset_Iterator::TYPE_PAGE;

    /**
     * The key(s) to use when paginating
     *
     * @var array
     */
    protected $_iteratorParams = array(
        'pageNumber' => false,
    );

    /**
     * Stored last request method
     *
     * @var string
     */
    protected $_lastRequestMethod;

    /**
     * Stored last request data
     *
     * @var array
     */
    protected $_lastRequestData;

    /**
     * @var array
     */
    protected $_writeCache = array(
        'add'         => array(),
        'update'      => array(),
        'addOrUpdate' => array(),
    );

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

        if (isset($this->_methodsByType['addOrUpdate'])) {
            unset($this->_writeCache['add']);
            unset($this->_writeCache['update']);
        } else {
            unset($this->_writeCache['addOrUpdate']);
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
        return new $rowClass($config);
    }

    /**
     * @param string $type
     * @param array $data
     * @param mixed $index
     * @return Bronto_Api_Object
     */
    public function addToWriteCache($type, array $data, $index = false)
    {
        if ($index) {
            $this->_writeCache[$type][$index] = $data;
        } else {
            $this->_writeCache[$type][] = $data;
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getWriteCache()
    {
        return $this->_writeCache;
    }

    /**
     * @return int
     */
    public function getWriteCacheSize()
    {
        $total = 0;
        foreach ($this->_writeCache as $type => $data) {
            $total += count($this->_writeCache[$type]);
        }
        return $total;
    }

    /**
     * Flush the write cache
     * @return Bronto_Api_Rowset|array
     */
    public function flush()
    {
        $result = array();
        foreach ($this->_writeCache as $type => $data) {
            if (!empty($data)) {
                $result[$type] = $this->{$type}(array_values($data));
                $this->_writeCache[$type] = array();
            }
        }
        return count($result) === 1 ? reset($result) : $result;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return Bronto_Api_Rowset
     */
    public function __call($name, $arguments)
    {
        switch ($name) {
            case 'add':
            case 'update':
            case 'delete':
            case 'addOrUpdate':
                $data   = $arguments[0];
                $method = $this->_methodsByType[$name];
                if (array_values($data) !== $data) {
                    $data = array($data);
                }
                return $this->doRequest($method, $data, true);
                break;
            case 'read':
                $data   = $arguments[0];
                $method = $this->_methodsByType[$name];
                return $this->doRequest($method, $data, false);
                break;
        }

        throw new BadMethodCallException("The method {$name} does not exist");
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
                        if ($retryer = $this->getApi()->getRetryer()) {
                            $retryer->store($this);
                        }
                    }
                    return $this->getApi()->throwException($exception);
                } else {
                    // Attempt to get a new session token
                    // sleep(5);
                    $this->getApi()->login();
                    // If using readDirection, we have to start over
                    if (isset($data['filter']['readDirection'])) {
                        $data['filter']['readDirection'] = self::DIRECTION_FIRST;
                    }
                }
            }

            if (!$error) {
                $success = true;
            }

        } while (!$success && $tries <= $maxTries);

        $result = isset($result->return) ? (array) $result->return : array();
        return $this->_parseResponse($result, $data);
    }

    /**
     * @param array $result
     * @param array $params
     * @return Bronto_Api_Rowset
     */
    public function _parseResponse(array $result, array $params = array())
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
            'params'    => $params,
        );

        $rowsetClass = $this->getRowsetClass();
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
        if ($this->_rowClass === null) {
            $className = get_class($this);
            $rowClass  = "{$className}_Row";
            if (class_exists($rowClass)) {
                $this->_rowClass = $rowClass;
            } else {
                $this->_rowClass = $this->_defaultRowClass;
            }
        }

        return $this->_rowClass;
    }

    /**
     * @return string
     */
    public function getRowsetClass()
    {
        if ($this->_rowsetClass === null) {
            $className = get_class($this);
            $rowsetClass = "{$className}_Rowset";
            if (class_exists($rowsetClass, false)) {
                $this->_rowsetClass = $rowsetClass;
            } else {
                $this->_rowsetClass = $this->_defaultRowsetClass;
            }
        }

        return $this->_rowsetClass;
    }

    /**
     * @return string
     */
    public function getExceptionClass()
    {
        if ($this->_exceptionClass === null) {
            $className = get_class($this);
            $exceptionClass = "{$className}_Exception";
            if (class_exists($exceptionClass)) {
                $this->_exceptionClass = $exceptionClass;
            } else {
                $this->_exceptionClass = $this->_defaultExceptionClass;
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
     * @return array
     */
    public function getIteratorParams()
    {
        return $this->_iteratorParams;
    }

    /**
     * @return bool
     */
    public function canIterate()
    {
        return $this->_iteratorType != Bronto_Api_Rowset_Iterator::TYPE_NONE;
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
