<?php
class QueryTest_Entry extends Doctrine_Record
{
    /**
     * Table structure.
     */
    public function setTableDefinition(): void
    {        
        $this->hasColumn('id', 'integer', 8, array('primary', 'autoincrement', 'notnull'));
        $this->hasColumn('authorId', 'integer', 8,
                array('notnull'));
        $this->hasColumn('date', 'integer', 8,
                array('notnull'));
    }

    /**
     * Runtime definition of the relationships to other entities.
     */
    public function setUp(): void
    {
        $this->hasOne('QueryTest_User as author', array(
            'local' => 'authorId', 'foreign' => 'id'
        ));
    }
}
