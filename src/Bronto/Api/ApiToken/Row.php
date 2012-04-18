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
        if ($this->permissions === null) {
            $this->read();
        }

        switch ($this->permissions) {
            case Bronto_Api_ApiToken::PERMISSION_READ:
                return array('read');
                break;
            case Bronto_Api_ApiToken::PERMISSION_WRITE:
                return array('write');
                break;
            case Bronto_Api_ApiToken::PERMISSION_SEND:
                return array('send');
                break;
            case Bronto_Api_ApiToken::PERMISSIONS_READ_WRITE:
                return array('read', 'write');
                break;
            case Bronto_Api_ApiToken::PERMISSIONS_READ_SEND:
                return array('read', 'send');
                break;
            case Bronto_Api_ApiToken::PERMISSIONS_WRITE_SEND:
                return array('write', 'send');
                break;
            case Bronto_Api_ApiToken::PERMISSIONS_READ_WRITE_SEND:
                return array('read', 'write', 'send');
                break;
        }

        return false;
    }
}