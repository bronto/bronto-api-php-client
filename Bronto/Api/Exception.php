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

    /* Custom */
    const EMPTY_RESULT          = 9001;

    protected $_recoverable = array(
        self::UNKNOWN_ERROR,
        self::INVALID_SESSION_TOKEN,
        self::INVALID_REQUEST,
        self::SHARD_OFFLINE,
        self::READ_ERROR
    );

    /**
     * @param type $message
     * @param type $code
     * @param type $previous
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        if (empty($code)) {
            $parts = explode(':', $message, 2);
            if (isset($parts[0]) && is_numeric($parts[0])) {
                $code    = $parts[0];
                $message = $parts[1];
            }
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
}