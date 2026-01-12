<?php
class CascadeDeleteRelatedTest extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
        $this->hasColumn('cscd_id', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasOne('CascadeDeleteTest', array('local' => 'cscd_id', 
                                                 'foreign' => 'id',
                                                 'onDelete' => 'CASCADE',
                                                 'onUpdate' => 'SET NULL'));

        $this->hasMany('CascadeDeleteRelatedTest2 as Related',
                        array('local' => 'id',
                              'foreign' => 'cscd_id'));
    }
}
