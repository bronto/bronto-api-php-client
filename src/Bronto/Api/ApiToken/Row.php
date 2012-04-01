<?php

/**
 * @property-read string $id
 * @property string $name
 * @property int $permissions
 * @property bool $active
 * @property string $accountId
 * @method Bronto_Api_ApiToken getApiObject() getApiObject()
 */
class Bronto_Api_ApiToken_Row extends Bronto_Api_Row
{
    /**
     * @return Bronto_Api_ApiToken_Row
     */
    public function read()
    {
        if ($this->id) {
            $params = array('id' => $this->id);
        } elseif ($this->name) {
            $params = array(
                'name' => array(
                    'value'    => $this->name,
                    'operator' => 'EqualTo',
                )
            );
        }

        parent::_read($params);
        return $this;
    }

    /**
     * @param bool $refresh
     * @return Bronto_Api_ApiToken_Row
     */
    public function save($refresh = false)
    {
        parent::_save(false, $refresh);
        return $this;
    }
}