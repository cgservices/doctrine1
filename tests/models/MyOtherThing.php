<?php
class MyOtherThing extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
        $this->hasColumn('user_id', 'integer', 8);
    }
    public function setUp(): void
    {
		$this->hasMany('MyUserOtherThing', array(
            'local' => 'id', 'foreign' => 'other_thing_id'
        ));
        
        $this->hasOne('MyUser', array(
            'local' => 'user_id', 'foreign' => 'id'
        ));
    }
}
