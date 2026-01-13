<?php
class QueryTest_Category extends Doctrine_Record
{    
    /**
     * The depth of the category inside the tree.
     * Non-persistent field. 
     * 
     * @var integer
     */
    public $depth;

    /**
     * Table definition.
     */
    public function setTableDefinition(): void
    {
        $this->hasColumn('id', 'integer', 8, array('primary', 'autoincrement', 'notnull'));
        $this->hasColumn('rootCategoryId as rootCategoryId', 'integer', 8, array('notnull' => false));
        $this->hasColumn('parentCategoryId as parentCategoryId', 'integer', 8, array('notnull' => false));
        $this->hasColumn('name as name', 'string', 50,
                array('notnull', 'unique'));
        $this->hasColumn('position as position', 'integer', 8,
                array('default' => 0, 'notnull'));
    }

    /**
     * Relations definition.
     */
    public function setUp(): void
    {
        $this->hasMany('QueryTest_Category as subCategories', array(
            'local' => 'id', 'foreign' => 'parentCategoryId'
        ));
        $this->hasOne('QueryTest_Category as rootCategory', array(
            'local' => 'rootCategoryId', 'foreign' => 'id'
        ));
        $this->hasMany('QueryTest_Board as boards', array(
            'local' => 'id', 'foreign' => 'categoryId', 'onDelete' => 'CASCADE'
        ));
    }
}
