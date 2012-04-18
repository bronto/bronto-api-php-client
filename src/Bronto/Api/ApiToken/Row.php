<?php

/**
 * @property-read string $id
 * @property string $name
 * @property int $permissions
 * @property bool $active
 * @property string $accountId
 * @method Bronto_Api_ApiToken_Row save() save()
 * @method Bronto_Api_ApiToken_Row delete() delete()
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
     * @return Bronto_Api_Account_Row
     */
    public function getAccount()
    {
        if (!$this->accountId) {
            if ($this->id || $this->name) {
                $this->read();
            }
            if (!$this->accountId) {
                throw new Bronto_Api_ApiToken_Exception('No accountId specified to retrieve Account');
            }
        }

        $account = $this->getApi()->getAccountObject()->createRow();
        $account->id = $this->accountId;
        $account->read();

        return $account;
    }

    /**
     * @param int $permissions
     * @return bool
     */
    public function hasPermissions($permissions)
    {
        if ($this->permissions === null) {
            $this->read();
        }

        return $this->permissions >= $permissions;
    }

    /**
     * @return array
     */
    public function getPermissionsLabels()
    {
        return $this->getApiObject()->getPermissionsLabels($this->permissions);
    }
}