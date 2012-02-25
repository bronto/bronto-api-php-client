<?php

/**
 * @property-read id
 * @property name
 * @property type
 * @property messagedId
 * @method Bronto_Api_Messagerule getApiObject()
 */
class Bronto_Api_Messagerule_Row extends Bronto_Api_Row
{
    /**
     * @param bool $returnData
     * @return Bronto_Api_Messagerule_Row|array
     */
    public function read($returnData = false)
    {
        if ($this->id) {
            $params = array('id' => $this->id);
        } else {
            $params = array(
                'name' => array(
                    'value'    => $this->name,
                    'operator' => 'EqualTo',
                )
            );
        }

        return parent::_read($params, $returnData);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return parent::_delete(array('id' => $this->id));
    }
}