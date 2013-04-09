<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_DeliveryGroup_Exception extends Bronto_Api_Exception
{
    const INVALID_DELIVERYGROUP                 = 801; // The specified deliverygroup was invalid.
    const DELIVERYGROUP_NO_ID                   = 802; // No ID provided for deliverygroup.
    const DELIVERYGROUP_DOES_NOT_EXIST          = 803; // Specified deliverygroup (id=%s) does not exist.
    const DELIVERYGROUP_ADD_FAIL                = 804; // Failed to add deliverygroup.
    const DELIVERYGROUP_LIST_FAIL               = 805; // Failed to list %s for deliverygroup (id=%s).
    const DELIVERYGROUP_ID_FAIL                 = 806; // Failed to find %s with id=%s in deliverygroup.
    const DELIVERYGROUP_IDS_FAIL                = 807; // Failed to find %s in deliverygroup.
    const DELIVERYGROUP_DELETE_FAIL             = 808; // Failed to remove deliverygroup.
    const DELIVERYGROUP_ADD_MEMBER_FAIL         = 809; // Failed to add element to deliverygroup.
    const DELIVERYGROUP_DELETE_MEMBER_FAIL      = 810; // Failed to remove element from deliverygroup.
    const DELIVERYGROUP_SEARCH_FAIL             = 811; // Search failed for query=%s.
    const DELIVERYGROUP_UPDATE_FAIL             = 812; // Failed to update deliverygroup.
    const DELIVERYGROUP_CREATED_ADD_MEMBER_FAIL	= 813; // Created deliverygroup but failed to add one or more elements to it
}
