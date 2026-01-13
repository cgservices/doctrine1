<?php
class Assignment extends Doctrine_Record {
    public function setTableDefinition(): void
    {
       $this->hasColumn('task_id', 'integer', 8); 
       $this->hasColumn('resource_id', 'integer', 8); 
    } 
}

