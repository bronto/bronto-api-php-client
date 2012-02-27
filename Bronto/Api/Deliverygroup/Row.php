<?php

/**
 * @property-read string $id
 * @property string $name
 * @property string $visibility
 * @property int $deliveryCount
 * @property string $createdDate
 * @property array $deliveryIds
 * @property array $messageRuleIds
 * @property array $messageIds
 * @method Bronto_Api_Deliverygroup getApiObject()
 */
class Bronto_Api_Deliverygroup_Row extends Bronto_Api_Row
{
    /**
     * @param bool $returnData
     * @return Bronto_Api_Deliverygroup_Row|array
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
     * @param bool $upsert
     * @return Bronto_Api_Deliverygroup_Row
     */
    public function save($upsert = true, $refresh = true)
    {
        if (!$upsert) {
            return parent::save($upsert, $refresh);
        }

        try {
            // Attempt to load DeliveryGroup
            $this->_refresh();
        } catch (Bronto_Api_Deliverygroup_Exception $e) {
            if ($e->getCode() == Bronto_Api_Exception::EMPTY_RESULT) {
                return parent::save($upsert, $refresh);
            } else {
                throw $e;
            }
        }
    }
}