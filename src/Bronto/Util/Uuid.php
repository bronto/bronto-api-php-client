<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 */
class Bronto_Util_Uuid
{
    /**
     * @param string $value
     * @return string
     */
    public function toString($value)
    {
        if ($value = $this->binaryToString($value)) {
            $this->unstrip($value);
        }
        return false;
    }

    /**
     * @param string $value
     * @return string
     */
    public function toBinary($value)
    {
        return $this->stringToBinary($this->strip($value));
    }

    /**
     * @param string $value
     * @return string
     */
    public function stringToBinary($value)
    {
        if (!empty($value)) {
            return pack('H*', $value);
        }
        return false;
    }

    /**
     * @param string $value
     * @return string
     */
    public function binaryToString($value)
    {
        if (!empty($value)) {
            $value = unpack('H*', $value);
            return array_shift($value);
        }
        return false;
    }

    /**
     * @param string $value
     * @return string
     */
    public function strip($value)
    {
        return str_replace('-', '', $value);
    }

    /**
     * @param string $value
     * @return string
     */
    public function unstrip($value)
    {
        if (strlen($value) === 32) {
            return preg_replace('/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/', '$1-$2-$3-$4-$5', $value);
        }

        return $value;
    }
}
