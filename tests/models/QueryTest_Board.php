<?php
class QueryTest_Board extends Doctrine_Record
{
    /**
     * Initializes the table definition.
     */
    public function setTableDefinition(): void
    {
        $this->hasColumn('id', 'integer', 8, array('primary', 'autoincrement', 'notnull'));
        $this->hasColumn('categoryId as categoryId', 'integer', 8,
                array('notnull'));
        $this->hasColumn('name as name', 'string', 100,
                array('notnull', 'unique'));
        $this->hasColumn('lastEntryId as lastEntryId', 'integer', 8,
                array('default' => 0));
        $this->hasColumn('position as position', 'integer', 8,
                array('default' => 0, 'notnull'));
    }

    /**
     * Initializes the relations.
     */
    public function setUp(): void
    {
        $this->hasOne('QueryTest_Category as category', array(
            'local' => 'categoryId', 'foreign' => 'id'
        ));
        $this->hasOne('QueryTest_Entry as lastEntry', array(
            'local' => 'lastEntryId', 'foreign' => 'id', 'onDelete' => 'CASCADE'
        ));
    }
}
