<?php
class Record_Country extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 200);
    }
    public function setUp(): void
    {
        $this->hasMany('Record_City as City', array(
            'local' => 'id',
            'foreign' => 'country_id'
        ));
    }
}


