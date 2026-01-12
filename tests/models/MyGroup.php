<?php
class MyGroup extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->setTableName('my_group');

        $this->hasColumn('id', 'integer', 8, array (  'primary' => true,  'autoincrement' => true,));
        $this->hasColumn('name', 'string', 255, array (  'notnull' => true,));
        $this->hasColumn('description', 'string', 4000, array ());
    }

    public function setUp(): void

    {
        $this->hasMany('MyUser as users', array('refClass' => 'MyUserGroup', 'local' => 'group_id', 'foreign' => 'user_id'));
    } 
}
