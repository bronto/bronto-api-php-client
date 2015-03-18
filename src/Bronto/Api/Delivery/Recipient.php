<?php
/**
 * @copyright  2011-2015 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
interface Bronto_Api_Delivery_Recipient
{
    /**
     * @return bool
     */
    public function isList();

    /**
     * @return bool
     */
    public function isContact();

    /**
     * @return bool
     */
    public function isSegment();
}
