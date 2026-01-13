<?php
class PackageVersionNotes extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('package_version_id', 'integer', 8);
        $this->hasColumn('description', 'string', 255);
    }
    public function setUp(): void
    {
        $this->hasOne('PackageVersion', array(
            'local' => 'package_version_id', 'foreign' => 'id'
        ));
    }
}
