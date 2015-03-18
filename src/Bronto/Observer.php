<?php

/**
 * @author Philip Cali <philip.cali@gmail.com>
 * @copyright  2011-2014 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface Bronto_Observer
{
    /**
     * Observe when the Bronto_Api is about to perform the login
     *
     * @param Bronto_Api $api
     * @return void
     */
    public function onBeforeLogin($api);

    /**
     * Observe when the Bronto_Api client makes a login call
     *
     * @param Bronto_Api $api
     * @param string $sessionId
     * @return void
     */
    public function onAfterLogin($api, $sessionId);

    /**
     * Observe when the Bronto_Api client throws an exception
     *
     * @param Bronto_Api $api
     * @param string $sessionId
     * @return void
     */
    public function onError($api, $exception);
}
