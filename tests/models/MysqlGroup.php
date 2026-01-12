<?php
class MysqlGroup extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', null);
    }
    
    public function setUp(): void
    
    {
        $this->hasMany('MysqlUser', array(
            'local' => 'group_id', 
            'foreign' => 'user_id',
            'refClass' => 'MysqlGroupMember'
        ));
    }
}
