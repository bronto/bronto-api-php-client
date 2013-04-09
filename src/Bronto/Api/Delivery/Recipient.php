<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
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
