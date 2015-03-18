<?php
/**
 * @copyright  2011-2015 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface Bronto_Util_Retryer_RetryerInterface
{
    /**
     * @param Bronto_Api_Object $object
     * @param int $attempts
     * @return string
     */
    function store(Bronto_Api_Object $object, $attempts = 0);

    /**
     * @param mixed $identifier
     * @return Bronto_Api_Rowset
     */
    function attempt($identifier);
}
