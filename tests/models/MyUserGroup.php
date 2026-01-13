<?php
class MyUserGroup extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->setTableName('my_user_group');
    
        $this->hasColumn('id', 'integer', 8, array (  'primary' => true,  'autoincrement' => true,));
        $this->hasColumn('group_id', 'integer', 8, array ());
        $this->hasColumn('user_id', 'integer', 8, array ());
    }
  
    public function setUp(): void
  
    {
        $this->hasOne('MyGroup as MyGroup', array('local' => 'group_id', 'foreign' => 'id', 'onDelete' => 'CASCADE'));
        $this->hasOne('MyUser as MyUser', array('local' => 'user_id', 'foreign' => 'id', 'onDelete' => 'CASCADE'));
    }
}
