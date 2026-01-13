<?php
class PackageVersion extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('package_id', 'integer', 8);
        $this->hasColumn('description', 'string', 255);
    }
    public function setUp(): void
    {
        $this->hasOne('Package', array('local' => 'package_id', 'foreign' => 'id'));
        $this->hasMany('PackageVersionNotes as Note', array(
            'local' => 'id', 'foreign' => 'package_version_id'
        ));
    }
}
