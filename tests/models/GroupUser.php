<?php
class Groupuser extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('added', 'integer', 8);
        $this->hasColumn('group_id', 'integer', 8);
        $this->hasColumn('user_id', 'integer', 8);
    }
    
    public function setUp(): void
    
    {
        $this->hasOne('Group', array('local' => 'group_id', 'foreign' => 'id'));
        $this->hasOne('User', array('local' => 'user_id', 'foreign' => 'id'));
    }
}
