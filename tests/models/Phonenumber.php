<?php
class Phonenumber extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('phonenumber', 'string',20);
        $this->hasColumn('entity_id', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasOne('Entity', array('local' => 'entity_id', 
                                      'foreign' => 'id', 
                                      'onDelete' => 'CASCADE'));
        
        $this->hasOne('Group', array('local' => 'entity_id', 
                                      'foreign' => 'id', 
                                      'onDelete' => 'CASCADE'));
          
        $this->hasOne('User', array('local' => 'entity_id', 
                                    'foreign' => 'id', 
                                    'onDelete' => 'CASCADE'));
    }
}
