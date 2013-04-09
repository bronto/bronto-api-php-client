<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @property string $username
 * @property string $password
 * @property stdClass $contactInformation
 * @property bool $permissionAgencyAdmin
 * @property bool $permissionAdmin
 * @property bool $permissionApi
 * @property bool $permissionUpgrade
 * @property bool $permissionFatigueOverride
 * @property bool $permissionMessageCompose
 * @property bool $permissionMessageDelete
 * @property bool $permissionAutomatorCompose
 * @property bool $permissionListCreateSend
 * @property bool $permissionListCreate
 * @property bool $permissionSegmentCreate
 * @property bool $permissionFieldCreate
 * @property bool $permissionFieldReorder
 * @property bool $permissionSubscriberCreate
 * @property bool $permissionSubscriberView
 * @method Bronto_Api_Login_Row read() read()
 * @method Bronto_Api_Login_Row save() save()
 * @method Bronto_Api_Login_Row delete() delete()
 * @method Bronto_Api_Login getApiObject() getApiObject()
 */
class Bronto_Api_Login_Row extends Bronto_Api_Row
{
    /**
     * @return Bronto_Api_Login_ContactInformation
     */
    public function getContactInformation()
    {
        return new Bronto_Api_Login_ContactInformation($this->contactInformation);
    }
}
