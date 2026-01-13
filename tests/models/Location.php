<?php
class Location extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('lat', 'double', 10, array ());
        $this->hasColumn('lon', 'double', 10, array ());
    }

    public function setUp(): void

    {
        $this->hasMany('LocationI18n as LocationI18n', array('local' => 'id', 'foreign' => 'id'));
    }
}
