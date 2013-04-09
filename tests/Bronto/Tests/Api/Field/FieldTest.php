<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @group object
 * @group field
 */
class Bronto_Tests_Api_Field_FieldTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_Field::createRow
     */
    public function testCreateWithCreateRow()
    {
        $field = $this->getObject()->createRow(array(
            'name'  => 'test_' . date('YmdHis'),
            'label' => 'Test Field ' . date('Y-m-d H:i:s'),
            'type'  => 'text',
        ));
        $field->save();

        $this->assertNotEmpty($field->id);
    }

    /**
     * @covers Bronto_Api_Field::createRow
     */
    public function testCreateWithCreateRowAndUpdate()
    {
        $field = $this->getObject()->createRow(array(
            'label' => 'Test Field ' . date('Y-m-d H:i:s'),
            'type'  => 'text',
        ));
        $field->name = 'test_' . date('YmdHis');
        $field->save();

        $this->assertNotEmpty($field->id);
    }

    /**
     * @covers Bronto_Api_Field::readAll
     */
    public function testReadAllFields()
    {
        $rowset = $this->getObject()->readAll();

        $this->assertGreaterThan(0, $rowset->count());
    }

    /**
     * @return Bronto_Api_Field
     */
    public function getObject()
    {
        return $this->getApi()->getFieldObject();
    }
}
