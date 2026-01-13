<?php
class CategoryWithPosition extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('position', 'integer', 8);
        $this->hasColumn('name', 'string', 255);
    }
    public function setUp(): void
    {
        $this->hasMany('BoardWithPosition as Boards', array('local' => 'id' , 'foreign' => 'category_id')); 
    }   
}
