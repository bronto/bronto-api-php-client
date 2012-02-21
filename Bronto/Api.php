<?php

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
        'no_refresh'         => false,
        'retry_limit'        => 5,
        // SoapClient
        'soap_version'       => null,
        'compression'        => null,
        'encoding'           => 'UTF-8',
        'trace'              => true,
        'connection_timeout' => null,
        'cache_wsdl'         => null,
        'user_agent'         => 'Bronto_Api <https://github.com/leek/bronto_service>',
        'features'           => null,
        'keep_alive'         => false,
    );

    /**
     * Cache of class objects
     *
     * @var array
     */
    protected $_classCache = array();

    /**
     * Constructor
     *
     * @param string $token
     * @param array $options
     */
    public function __construct($token = null, array $options = array())
    {
        if (!extension_loaded('soap')) {
            require_once 'Bronto/Api/Exception.php';
            throw new Bronto_Api_Exception('SOAP extension is not loaded.');
        }

        if (!extension_loaded('openssl')) {
            require_once 'Bronto/Api/Exception.php';
            throw new Bronto_Api_Exception('OpenSSL extension is not loaded.');
        }

        if ($token !== null) {
            $this->setToken($token);
        }

        if (!empty($options)) {
            $this->setOptions($options);
        }

        // Use SOAP 1.2 as default
        if ($this->_options['soap_version'] == null) {
            $this->_options['soap_version'] = SOAP_1_2;
        }

        // Accept GZIP compression
        if ($this->_options['compression'] == null) {
            $this->_options['compression'] = SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_DEFLATE;
        }

        // No WSDL cache
        if ($this->_options['cache_wsdl'] == null) {
            $this->_options['cache_wsdl'] = WSDL_CACHE_NONE;
        }

        if ($this->_options['features'] == null) {
            $this->_options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
        }
    }

    /**
     * Login with API token
     *
     * @return Bronto_Api
     */
    public function login()
    {
        try {
            $client    = $this->getSoapClient();
            $sessionId = $client->login(array('apiToken' => $this->getToken()))->return;
            $client->__setSoapHeaders(array(
                new SoapHeader(self::BASE_URL, 'sessionHeader', array('sessionId' => $sessionId))
            ));
        } catch (SoapFault $e) {
            if (strpos($e->getMessage(), 'Authentication failed for token') !== false) {
                throw new Bronto_Api_Exception("Authentication failed for token: {$this->getToken()}");
            }
        }

        return $this;
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
                        throw new Bronto_Api_Exception('Invalid soap_version specified. Use SOAP_1_1 or SOAP_1_2 constants.');
                    }
                    break;
            }

            $this->_options[$name] = $value;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getOption($name)
    {
        if (isset($this->_options[$name])) {
            return $this->_options[$name];
        }
        return false;
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
     * @return Bronto_Api_Deliverygroup
     */
    public function getDeliveryGroupObject()
    {
        return $this->getObject('deliverygroup');
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
     * @return Bronto_Api_Messagerule
     */
    public function getMessageRuleObject()
    {
        return $this->getObject('messagerule');
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
     * @return Bronto_Api_Abstract
     */
    public function getObject($object)
    {
        $object = ucfirst($object);

        if (!isset($this->_classCache[$object])) {
            $className = "Bronto_Api_{$object}";
            if (class_exists($className)) {
                $this->_classCache[$object] = new $className(array('api' => $this));
            } else {
                throw new Exception("Unable to load class: {$className}");
            }
        }

        return $this->_classCache[$object];
    }

    /**
     * @return SoapClient
     */
    public function getSoapClient()
    {
        if ($this->_soapClient == null) {
            $this->_soapClient = new SoapClient(self::BASE_WSDL, array(
                'soap_version' => $this->_options['soap_version'],
                'compression'  => $this->_options['compression'],
                'encoding'     => $this->_options['encoding'],
                'trace'        => $this->_options['trace'],
                'cache_wsdl'   => $this->_options['cache_wsdl'],
                'user_agent'   => $this->_options['user_agent'],
                'features'     => $this->_options['features'],
                'keep_alive'   => $this->_options['keep_alive'],
            ));
            $this->_soapClient->__setLocation(self::BASE_LOCATION);
        }
        return $this->_soapClient;
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
}