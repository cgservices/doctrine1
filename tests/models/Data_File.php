<?php
class Data_File extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('filename', 'string');
        $this->hasColumn('file_owner_id', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasOne('File_Owner', array('local' => 'file_owner_id', 'foreign' => 'id'));
    }
}
