<?php
class MyUserOneThing extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('user_id', 'integer', 8);
        $this->hasColumn('one_thing_id', 'integer', 8);
    }
    
    
    public function setUp(): void
    
    
    {
        $this->hasOne('MyUser', array(
            'local' => 'user_id', 'foreign' => 'id'
        ));
        
        $this->hasOne('MyOneThing', array(
            'local' => 'one_thing_id', 'foreign' => 'id'
        ));
    }
}
