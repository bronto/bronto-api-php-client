<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 */
class Bronto_Api
{
    const BASE_WSDL     = 'https://api.bronto.com/v4?wsdl';
    const BASE_LOCATION = 'https://api.bronto.com/v4';
    const BASE_URL      = 'http://api.bronto.com/v4';

    /**
     * SoapClient object
     *
     * @var SoapClient
     */
    protected $_soapClient;

    /**
     * API token
     *
     * @var string
     */
    protected $_token;

    /**
     * @var array
     */
    protected $_options = array(
        // Bronto
        'refresh_on_save'    => false,
        'retry_limit'        => 5,
        'debug'              => true,
        'retryer'            => array(
            'type' => null,
            'path' => null,
        ),
        // SoapClient
        'soap_version'       => null,
        'compression'        => null,
        'encoding'           => 'UTF-8',
        'trace'              => false,
        'exceptions'         => true,
        'cache_wsdl'         => false,
        'user_agent'         => 'Bronto_Api <https://github.com/leek/bronto_service>',
        'features'           => null,
        'connection_timeout' => 30,
    );

    /**
     * Cache of class objects
     *
     * @var array
     */
    protected $_classCache = array();

    /**
     * @var bool
     */
    protected $_connected = false;

    /**
     * @var bool
     */
    protected $_authenticated = false;

    /**
     * @var Bronto_Api_Retryer_RetryerInterface
     */
    protected $_retryer;

    /**
     * @param string $token
     * @param array $options
     */
    public function __construct($token = null, array $options = array())
    {
        if (!extension_loaded('soap')) {
            throw new Bronto_Api_Exception('SOAP extension is not loaded.');
        }

        if (!extension_loaded('openssl')) {
            throw new Bronto_Api_Exception('OpenSSL extension is not loaded.');
        }

        if ($token !== null) {
            $this->setToken($token);
        }

        if (!empty($options)) {
            $this->setOptions($options);
        }

        // Turn on trace if debug is enabled
        if ($this->_options['debug']) {
            $this->_options['trace'] = true;
        }

        // Use SOAP 1.1 as default
        if ($this->_options['soap_version'] == null) {
            $this->_options['soap_version'] = SOAP_1_1;
        }

        // Accept GZIP compression
        if ($this->_options['compression'] == null) {
            $this->_options['compression'] = SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP;
        }

        // Turn on the WSDL cache
        if ($this->_options['cache_wsdl'] === false) {
            $this->_options['cache_wsdl'] = WSDL_CACHE_NONE;
        } elseif ($this->_options['cache_wsdl'] == null) {
            $this->_options['cache_wsdl'] = WSDL_CACHE_BOTH;
        }

        if ($this->_options['features'] == null) {
            $this->_options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
        }

        ini_set('default_socket_timeout', 120);
    }

    /**
     * Login with API token
     *
     * @return Bronto_Api
     */
    public function login()
    {
        $token = $this->getToken();
        if (empty($token)) {
            throw new Bronto_Api_Exception('Token is empty or invalid.', Bronto_Api_Exception::NO_TOKEN);
        }

        try {
            $this->_authenticated = false;
            $this->_soapClient    = null;

            // Get a new SoapClient
            $client    = $this->getSoapClient(false);
            $sessionId = $client->login(array('apiToken' => $token))->return;
            $client->__setSoapHeaders(array(
                new SoapHeader(self::BASE_URL, 'sessionHeader', array('sessionId' => $sessionId))
            ));
            $this->_authenticated = true;
        } catch (Exception $e) {
            $this->throwException($e);
        }

        return $this;
    }

    /**
     * We want all Exceptions to be Bronto_Api_Exception for request/response
     *
     * @param string|Exception $exception
     * @param string $message
     * @param string $code
     * @return Bronto_Api_Exception
     */
    public function throwException($exception, $message = null, $code = null)
    {
        if ($exception instanceOf Exception) {
            if ($exception instanceOf Bronto_Api_Exception) {
                // Good
            } else {
                $exception = new Bronto_Api_Exception($exception->getMessage(), $exception->getCode(), null, $e);
            }
        } else {
            if (is_string($exception)) {
                if (class_exists($exception, false)) {
                    $exception = new $exception($message, $code);
                } else {
                    $exception = new Bronto_Api_Exception($exception);
                }
            }
        }

        if ($this->getDebug()) {
            /* @var $exception Bronto_Api_Exception */
            $exception->setRequest($this->getLastRequest());
            $exception->setResponse($this->getLastResponse());
        }

        throw $exception;
    }

    /**
     * Set API token
     *
     * @param string $token
     * @return Bronto_Api
     */
    public function setToken($token)
    {
        $this->_token = $token;
        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * @param array $options
     * @return Bronto_Api
     */
    public function setOptions(array $options = array())
    {
        foreach ($options as $name => $value) {
            $this->setOption($name, $value);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Bronto_Api
     */
    public function setOption($name, $value)
    {
        if (isset($this->_options[$name])) {
            // Some settings need checked
            switch ($name) {
                case 'soap_version':
                    if (!in_array($value, array(SOAP_1_1, SOAP_1_2))) {
                        throw new Bronto_Api_Exception('Invalid soap_version value specified. Use SOAP_1_1 or SOAP_1_2 constants.');
                    }
                    break;
                case 'cache_wsdl':
                    if (!in_array($value, array(WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY, WSDL_CACHE_BOTH))) {
                        throw new Bronto_Api_Exception('Invalid cache_wsdl value specified.');
                    }
                    break;
            }

            $this->_options[$name] = $value;
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        }
        return $default;
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Activity
     */
    public function getActivityObject()
    {
        return $this->getObject('activity');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Contact
     */
    public function getContactObject()
    {
        return $this->getObject('contact');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Conversion
     */
    public function getConversionObject()
    {
        return $this->getObject('conversion');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Delivery
     */
    public function getDeliveryObject()
    {
        return $this->getObject('delivery');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_DeliveryGroup
     */
    public function getDeliveryGroupObject()
    {
        return $this->getObject('deliveryGroup');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Field
     */
    public function getFieldObject()
    {
        return $this->getObject('field');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Message
     */
    public function getMessageObject()
    {
        return $this->getObject('message');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_MessageRule
     */
    public function getMessageRuleObject()
    {
        return $this->getObject('messageRule');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_List
     */
    public function getListObject()
    {
        return $this->getObject('list');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Segment
     */
    public function getSegmentObject()
    {
        return $this->getObject('segment');
    }

    /**
     * Lazy loads our API objects
     *
     * @param string $object
     * @return Bronto_Api_Object
     */
    public function getObject($object)
    {
        $object = ucfirst($object);

        if (!isset($this->_classCache[$object])) {
            $className = "Bronto_Api_{$object}";
            if (class_exists($className)) {
                $this->_classCache[$object] = new $className(array('api' => $this));
            } else {
                $this->throwException("Unable to load class: {$className}");
            }
        }

        return $this->_classCache[$object];
    }

    /**
     * @param bool $authenticate
     * @return SoapClient
     */
    public function getSoapClient($authenticate = true)
    {
        if ($this->_soapClient == null) {
            $this->_connected = false;
            $this->_soapClient = new SoapClient(self::BASE_WSDL, array(
                'soap_version' => $this->_options['soap_version'],
                'compression'  => $this->_options['compression'],
                'encoding'     => $this->_options['encoding'],
                'trace'        => $this->_options['trace'],
                'exceptions'   => $this->_options['exceptions'],
                'cache_wsdl'   => $this->_options['cache_wsdl'],
                'user_agent'   => $this->_options['user_agent'],
                'features'     => $this->_options['features'],
            ));
            $this->_soapClient->__setLocation(self::BASE_LOCATION);
            $this->_connected = true;
            if ($authenticate && !$this->isAuthenticated() && $this->getToken()) {
                $this->login();
            }
        }
        return $this->_soapClient;
    }

    /**
     * @param bool $value
     * @return Bronto_Api
     */
    public function setDebug($value)
    {
        $this->_options['debug'] = (bool) $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDebug()
    {
        return $this->_options['debug'];
    }

    /**
     * @param array $options
     * @return Bronto_Api_Retryer_RetryerInterface
     */
    public function getRetryer(array $options = array())
    {
        if (!($this->_retryer instanceOf Bronto_Api_Retryer_RetryerInterface)) {
            $options = array_merge($this->_options['retryer'], $options);
            switch ($options['type']) {
                case 'file':
                    $this->_retryer = new Bronto_Api_Retryer_FileRetryer($options);
                    break;
                default:
                    return false;
                    break;
            }
        }

        return $this->_retryer;
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return (bool) $this->_connected;
    }

    /**
     * @return bool
     */
    public function isAuthenticated()
    {
        return (bool) $this->_authenticated;
    }

    /**
     * Seamlessly iterate over a rowset.
     *
     * @param Bronto_Api_Rowset $rowset
     * @return Bronto_Api_Rowset_Iterator
     */
    public function iterate(Bronto_Api_Rowset $rowset)
    {
        return new Bronto_Api_Rowset_Iterator($rowset);
    }

    /**
     * Retrieve request XML
     *
     * @return string
     */
    public function getLastRequest()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastRequest();
        }
        return '';
    }

    /**
     * Get response XML
     *
     * @return string
     */
    public function getLastResponse()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastResponse();
        }
        return '';
    }

    /**
     * Retrieve request headers
     *
     * @return string
     */
    public function getLastRequestHeaders()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastRequestHeaders();
        }
        return '';
    }

    /**
     * Retrieve response headers (as string)
     *
     * @return string
     */
    public function getLastResponseHeaders()
    {
        if ($this->_soapClient !== null) {
            return $this->_soapClient->__getLastResponseHeaders();
        }
        return '';
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        return array(
            '_token',
            '_options',
        );
    }
}