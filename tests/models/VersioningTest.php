<?php
class VersioningTest extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
        $this->hasColumn('version', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->actAs('Versionable');
    }
}

class VersioningTest2 extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
        // version column is added by Versionable behavior
    }
    public function setUp(): void
    {
        $this->actAs('Versionable', array('auditLog' => false));
    }
}

class VersioningTest3 extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string');
        $this->hasColumn('version', 'integer', 8);
    }
    public function setUp(): void
    {
    	  
        $this->actAs('Versionable', array('tableName' =>  'tbl_prefix_comments_version',
                                          'className' =>  'VersioningTestClass'));

    }
}