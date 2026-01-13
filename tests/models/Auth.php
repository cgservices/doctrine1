<?php
class Auth extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('roleid', 'integer', 10);
        $this->hasColumn('name', 'string', 50);
    }
    public function setUp(): void
    {
        $this->hasOne('Role', array('local' => 'roleid', 'foreign' => 'id'));
    }
}

