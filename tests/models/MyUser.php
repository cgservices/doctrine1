<?php
class MyUser extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
    }
    
    public function setUp(): void
    
    {
		  $this->hasMany('MyOneThing', array(
            'local' => 'id', 'foreign' => 'user_id'
          ));

		  $this->hasMany('MyOtherThing', array(
            'local' => 'id', 'foreign' => 'user_id'
          ));
    }
}