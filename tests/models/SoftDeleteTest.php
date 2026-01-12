<?php
class SoftDeleteTest extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('id', 'integer', 8, array('primary' => true, 'autoincrement' => true));
        $this->hasColumn('name', 'string', 255, array('notnull' => true));
        $this->hasColumn('something', 'string', 25, array('notnull' => true, 'unique' => true));
    }

    public function setUp(): void

    {
        $this->actAs('SoftDelete');
    }
}