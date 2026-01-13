<?php
class Record_City extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 200);
        $this->hasColumn('country_id', 'integer', 8);
        $this->hasColumn('district_id', 'integer', 8);
    }
    
    public function setUp(): void
    
    {
        $this->hasOne('Record_Country as Country', array(
            'local' => 'country_id', 'foreign' => 'id'
        ));

        $this->hasOne('Record_District as District', array(
            'local' => 'district_id', 'foreign' => 'id'
        ));
    }
}
