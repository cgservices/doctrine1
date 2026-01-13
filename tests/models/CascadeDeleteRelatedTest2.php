<?php
class CascadeDeleteRelatedTest2 extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
        $this->hasColumn('cscd_id', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasOne('CascadeDeleteRelatedTest', array('local' => 'cscd_id',
                                                        'foreign' => 'id',
                                                        'onDelete' => 'SET NULL'));
    }
}
