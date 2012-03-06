<?php

class Bronto_Api_Exception extends Exception
{
    const UNKNOWN_ERROR         = 101; // There was an unknown API error. Please try your request again shortly.
    const INVALID_TOKEN         = 102; // Authentication failed for token:
    const INVALID_SESSION_TOKEN	= 103; // Your session is invalid. Please log in again.
    const INVALID_ACCESS        = 104; // You do not have valid access for this method.
    const INVALID_INPUT_ARRAY   = 105; // You must specify at least one item in the input array.
    const INVALID_PARAMETER	    = 106; // Unable to verify parameter:
    const INVALID_REQUEST	    = 107; // There was an error in your soap request. Please examine the request and try again.
    const SHARD_OFFLINE         = 108; // The API is currently undergoing maintenance. Please try your request again later.
    const SITE_INACTIVE         = 109; // This site is currently marked as 'inactive'
    const REQUIRED_FIELDS	    = 110; // Required fields are missing:
    const UNAUTHORIZED_IP	    = 111; // Your IP address does not have access for token.
    const INVALID_FILTER	    = 112; // Invalid filter type (must be AND or OR).
    const READ_ERROR            = 113; // There was an error reading your query results. Please try your request again shortly.

    /* Misc */
    const HTTP_HEADER_ERROR     = 98001; // Error Fetching http headers
    const NO_XML_DOCUMENT       = 98002;
    const INVALID_URL           = 98003;
    const CONNECT_ERROR         = 98004;
    const WSDL_PARSE_ERROR      = 98005;
    const REQUEST_ERROR         = 98006;
    const CONNECTION_RESET      = 98007; // SSL: Connection reset by peer

    /* Custom */
    const EMPTY_RESULT          = 99001;
    const NO_TOKEN              = 99002;

    /**
     * Array of exceptions we can (maybe) recover from
     * @var array
     */
    protected $_recoverable = array(
        self::UNKNOWN_ERROR,
        self::INVALID_SESSION_TOKEN,
        self::INVALID_REQUEST,
        self::SHARD_OFFLINE,
        self::READ_ERROR,
        self::HTTP_HEADER_ERROR,
        self::NO_XML_DOCUMENT,
        self::CONNECT_ERROR,
        self::WSDL_PARSE_ERROR,
        self::CONNECTION_RESET,
    );

    /**
     * @var string
     */
    protected $_request;

    /**
     * @var string
     */
    protected $_response;

    /**
     * @param string $message
     * @param string $code
     * @param Exception $previous
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        if (empty($code)) {
            $parts = explode(':', $message, 2);
            if (is_array($parts)) {
                $parts = array_map('trim', $parts);
            }
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $code    = (int)    $parts[0];
                $message = (string) $parts[1];
            }
        }

        if (empty($code)) {
            // Handle some SoapFault exceptions
            if (stripos($message, 'Error Fetching http headers') !== false) {
                $code = self::HTTP_HEADER_ERROR;
            } else if (stripos($message, 'looks like we got no XML document') !== false) {
                $code = self::NO_XML_DOCUMENT;
            } else if (stripos($message, 'Could not connect to host') !== false) {
                $code = self::CONNECT_ERROR;
            } else if (stripos($message, 'Parsing WSDL') !== false) {
                $code = self::WSDL_PARSE_ERROR;
            } else if (stripos($message, 'There was an error in your soap request') !== false) {
                $code = self::REQUEST_ERROR;
            } else if (stripos($message, 'Connection reset by peer') !== false) {
                $code = self::CONNECTION_RESET;
            } else if (stripos($message, 'Unable to parse URL') !== false) {
                $code = self::INVALID_URL;
            }
        }

        if (!empty($code)) {
            $message = "{$code} : {$message}";
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return bool
     */
    public function isRecoverable()
    {
        if (!$this->getCode()) {
            return false;
        }
        return in_array($this->getCode(), $this->_recoverable);
    }

    /**
     * @param int $tries
     * @return Bronto_Api_Exception
     */
    public function setTries($tries)
    {
        $this->message = $this->message . " [Tried: {$tries}]";
        return $this;
    }

    /**
     * @param string $request
     * @return Bronto_Api_Exception
     */
    public function setRequest($request)
    {
        $this->_request = $request;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @param string $response
     * @return Bronto_Api_Exception
     */
    public function setResponse($response)
    {
        $this->_response = $response;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->_response;
    }
}