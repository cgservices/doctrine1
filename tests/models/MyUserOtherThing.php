<?php
class MyUserOtherThing extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('user_id', 'integer', 8);
        $this->hasColumn('other_thing_id', 'integer', 8);
    }
    
    
    public function setUp(): void
    
    
    {
        $this->hasOne('MyUser', array(
            'local' => 'user_id', 'foreign' => 'id'
        ));
        
        $this->hasOne('MyOtherThing', array(
            'local' => 'other_thing_id', 'foreign' => 'id'
        ));
    }
}
