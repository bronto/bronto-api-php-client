<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/messageobject
 *
 * @method Bronto_Api_Message_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Message extends Bronto_Api_Object
{
    /**
     * @var array
     */
    protected $_methods = array(
        'addMessages'    => 'add',
        'readMessages'   => 'read',
        'updateMessages' => 'update',
        'deleteMessages' => 'delete',
    );

    /**
     * @param array $filter
     * @param bool $includeContent
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), $includeContent = false, $pageNumber = 1)
    {
        $params = array();
        $params['filter']         = $filter;
        $params['includeContent'] = (bool) $includeContent;
        $params['pageNumber']     = (int)  $pageNumber;
        return $this->read($params);
    }
}