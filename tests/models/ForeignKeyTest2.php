<?php
class ForeignKeyTest2 extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', null);
        $this->hasColumn('foreignkey', 'integer', 8);
       
        $this->hasOne('ForeignKeyTest', array(
            'local' => 'foreignKey', 'foreign' => 'id'
        ));
    }
}
