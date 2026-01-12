<?php
class QueryTest_Subscription extends Doctrine_Record
{
    public function setTableDefinition(): void
    {   $this->hasColumn('id', 'integer', 8, array('primary', 'autoincrement', 'notnull'));     
        $this->hasColumn('begin', 'date');
        $this->hasColumn('end', 'date');
    }
}
