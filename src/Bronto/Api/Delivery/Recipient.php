<?php

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