<?php
class MyOneThing extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
        $this->hasColumn('user_id', 'integer', 8);
    }

    public function setUp(): void

    {
		$this->hasMany('MyUserOneThing', array(
            'local' => 'id', 'foreign' => 'one_thing_id'
        ));
        
        $this->hasOne('MyUser', array(
            'local' => 'user_id', 'foreign' => 'id'
        ));
    }
}
