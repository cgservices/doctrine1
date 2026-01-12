<?php
class Log_Entry extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('stamp', 'timestamp');
        $this->hasColumn('status_id', 'integer', 8);
    }
    
    public function setUp(): void
    
    {
        $this->hasOne('Log_Status', array(
            'local' => 'status_id', 'foreign' => 'id'
        ));
    }
}
