<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api
{
    /** URI */
    const BASE_WSDL     = 'https://api.bronto.com/v4?wsdl';
    const BASE_LOCATION = 'https://api.bronto.com/v4';
    const BASE_URL      = 'http://api.bronto.com/v4';

    /**
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
        'soap_client'     => 'Bronto_SoapClient',
        'refresh_on_save' => false,
        'retry_limit'     => 5,
        'debug'           => false,
        'retryer'         => array(
            'type' => null,
            'path' => null,
        ),
        'observer'        => false,
        // SoapClient
        'soap_version'       => SOAP_1_1,
        'compression'        => true,
        'encoding'           => 'UTF-8',
        'trace'              => false,
        'exceptions'         => true,
        'cache_wsdl'         => WSDL_CACHE_BOTH,
        'user_agent'         => 'Bronto_Api <https://github.com/leek/bronto_service>',
        'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
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
     * @var Bronto_Util_Retryer_RetryerInterface
     */
    protected $_retryer;

    /**
     * @var Bronto_Util_Uuid
     */
    protected $_uuid;

    /**
     * @var Bronto_Observer
     */
    protected $_observer;

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

        $this->_options['compression'] = SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP;
        $this->_setOptions($options);

        if ($token !== null) {
            $this->setToken($token);
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
            // Get a new SoapClient
            $this->reset();
            $client    = $this->getSoapClient(false);
            // Allow observer to inject a session before login
            if ($this->getObserver()) {
                $this->getObserver()->onBeforeLogin($this);
            }
            // Check for auth changes
            if (!$this->isAuthenticated()) {
                $sessionId = $client->login(array('apiToken' => $token))->return;
                $this->setSessionId($sessionId);

                // Allow observer to store session
                if ($this->getObserver()) {
                    $this->getObserver()->onAfterLogin($this, $sessionId);
                }
            }
        } catch (Exception $e) {
            $this->throwException($e);
        }

        return $this;
    }

    /**
     * Resuse an existing session, if possible
     *
     * @param string $sessionId
     * @return Bronto_Api
     */
    public function setSessionId($sessionId)
    {
        $client = $this->getSoapClient(false);
        $client->__setSoapHeaders(array(
            new SoapHeader(self::BASE_URL, 'sessionHeader', array('sessionId' => $sessionId))
        ));
        $this->_authenticated = true;
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
                // Convert
                $exception = new Bronto_Api_Exception($exception->getMessage(), $exception->getCode(), null, $exception);
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

        // For tracking request/response in debug mode
        if ($this->getDebug()) {
            /* @var $exception Bronto_Api_Exception */
            $exception->setRequest($this->getLastRequest());
            $exception->setResponse($this->getLastResponse());
        }

        // Allow observer to handle exception cases
        if ($this->getObserver()) {
            $this->getObserver()->onError($this, $exception);
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
        $this->reset();
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
     * @return Bronto_Api_ApiToken_Row
     */
    public function getTokenInfo()
    {
        $apiToken = $this->getApiTokenObject()->createRow();
        $apiToken->id = $this->getToken();
        $apiToken->read();

        return $apiToken;
    }

    /**
     * @param array $options
     * @return Bronto_Api
     */
    protected function _setOptions(array $options = array())
    {
        foreach ($options as $name => $value) {
            $this->_setOption($name, $value);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Bronto_Api
     */
    protected function _setOption($name, $value)
    {
        if (isset($this->_options[$name])) {
            // Some settings need checked
            switch ($name) {
                case 'soap_client':
                    if (!class_exists($value)) {
                        $this->throwException("Unable to load class: {$value} as SoapClient.");
                    }
                    break;
                case 'soap_version':
                    if (!in_array($value, array(SOAP_1_1, SOAP_1_2))) {
                        $this->throwException('Invalid soap_version value specified. Use SOAP_1_1 or SOAP_1_2 constants.');
                    }
                    break;
                case 'cache_wsdl':
                    if (!in_array($value, array(WSDL_CACHE_NONE, WSDL_CACHE_DISK, WSDL_CACHE_MEMORY, WSDL_CACHE_BOTH))) {
                        $this->throwException('Invalid cache_wsdl value specified.');
                    }
                    // If debug mode, ignore WSDL cache setting
                    if ($this->getDebug()) {
                        $value = WSDL_CACHE_NONE;
                    }
                    break;
                case 'debug':
                    if ($value == true) {
                        $this->_options['trace']      = true;
                        $this->_options['cache_wsdl'] = WSDL_CACHE_NONE;
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
     * @return Bronto_Api_Account
     */
    public function getAccountObject()
    {
        return $this->getObject('account');
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
     * @return Bronto_Api_ApiToken
     */
    public function getApiTokenObject()
    {
        return $this->getObject('apiToken');
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
     * @return Bronto_Api_ContentTag
     */
    public function getContentTagObject()
    {
        return $this->getObject('contentTag');
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
     * @return Bronto_Api_Login
     */
    public function getLoginObject()
    {
        return $this->getObject('login');
    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Order
     */
    public function getOrderObject()
    {
        return $this->getObject('order');
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
            $soapClientClass  = $this->getOption('soap_client', 'Bronto_SoapClient');
            $this->_soapClient = new $soapClientClass(self::BASE_WSDL, array(
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
            if ($authenticate && !$this->_authenticated && $this->getToken()) {
                $this->login();
            }
        }
        return $this->_soapClient;
    }

    /**
     * @return Bronto_Api
     */
    public function reset()
    {
        $this->_connected     = false;
        $this->_authenticated = false;
        $this->_soapClient    = null;
        return $this;
    }

    /**
     * @param bool $value
     * @return Bronto_Api
     */
    public function setDebug($value)
    {
        return $this->_setOption('debug', (bool) $value);
    }

    /**
     * @return bool
     */
    public function getDebug()
    {
        return (bool) $this->_options['debug'];
    }

    /**
     * @param array $options
     * @return Bronto_Util_Retryer_RetryerInterface
     */
    public function getRetryer(array $options = array())
    {
        if (!($this->_retryer instanceOf Bronto_Util_Retryer_RetryerInterface)) {
            $options = array_merge($this->_options['retryer'], $options);
            switch ($options['type']) {
                case 'custom':
                    if ($options['object']) {
                        $this->_retryer = $options['object'];
                    } else {
                        $this->_retryer = new $options['path'];
                    }
                    break;
                case 'file':
                    $this->_retryer = new Bronto_Util_Retryer_FileRetryer($options);
                    break;
                default:
                    return false;
                    break;
            }
        }

        return $this->_retryer;
    }

    /**
     * Gets the observer for the API client
     *
     * @return Bronto_Observer
     */
    public function getObserver()
    {
        if (!$this->_observer) {
            if (isset($this->_options['observer'])) {
                $observer = $this->_options['observer'];
                if (is_string($observer) && class_exists($observer)) {
                    $observer = new $observer();
                }
                if ($observer instanceOf Bronto_Observer) {
                    $this->_observer = $observer;
                }
            }
        }
        return $this->_observer;
    }

    /**
     * @return Bronto_Util_Retryer_RetryerInterface
     */
    public function getUuid()
    {
        if (!$this->_uuid) {
            $this->_uuid = new Bronto_Util_Uuid();
        }

        return $this->_uuid;
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
