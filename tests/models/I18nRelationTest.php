<?php
class I18nRelationTest extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('title', 'string', 200);
        $this->hasColumn('author_id', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasOne('I18nAuthorTest', array('local' => 'author_id',
                                    'foreign' => 'id'));
        $this->actAs('I18n', array('fields' => array('author_id', 'title')));
    }
}

class I18nAuthorTest extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('id', 'integer', 8, array('primary' => true, 'autoincrement' => true));
    }
    public function setUp(): void
    {
        $this->hasMany('I18nRelationTest', array('local' => 'id',
                                    'foreign' => 'author_id'));
    }
}