<?php
class QueryTest_UserRank extends Doctrine_Record
{
    public function setTableDefinition(): void
    {        
        $this->hasColumn('rankId', 'integer', 8, array('primary' => true));
        $this->hasColumn('userId', 'integer', 8, array('primary' => true));
    }
}
