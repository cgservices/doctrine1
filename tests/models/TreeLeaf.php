<?php
class TreeLeaf extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
    	$this->hasColumn('name', 'string');
        $this->hasColumn('parent_id', 'integer', 8);
    }

    public function setUp(): void

    {
        $this->hasOne('TreeLeaf as Parent', array(
            'local' => 'parent_id', 'foreign' => 'id'
        ));
        
        $this->hasMany('TreeLeaf as Children', array(
            'local' => 'id', 'foreign' => 'parent_id'
        ));
    }
}
