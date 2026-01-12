<?php
class FooForeignlyOwnedWithPk extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 200);
    }
    public function setUp(): void
    {
        $this->hasOne('FooRecord', array('local' => 'id', 'foreign' => 'id'));
    }
}
