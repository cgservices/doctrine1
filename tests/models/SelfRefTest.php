<?php
class SelfRefTest extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 50);
        $this->hasColumn('created_by', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasOne('SelfRefTest as createdBy', array('local' => 'created_by'));
    }
}

