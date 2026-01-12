<?php
class M2MTest extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 200);
        $this->hasColumn('child_id', 'integer', 8);
    }
    public function setUp(): void
    {

        $this->hasMany('RTC1 as RTC1', array('local' => 'c1_id', 'foreign' => 'c1_id', 'refClass' => 'JC1'));
        $this->hasMany('RTC2 as RTC2', array('local' => 'c1_id', 'foreign' => 'c1_id', 'refClass' => 'JC1'));
        $this->hasMany('RTC3 as RTC3', array('local' => 'c1_id', 'foreign' => 'c1_id', 'refClass' => 'JC2'));
        $this->hasMany('RTC3 as RTC4', array('local' => 'c1_id', 'foreign' => 'c1_id', 'refClass' => 'JC1'));

    }
}

