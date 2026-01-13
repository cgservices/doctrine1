<?php
class Book extends Doctrine_Record
{
    public function setUp(): void
    {
        $this->hasMany('Author', array('local' => 'id', 'foreign' => 'book_id'));
        $this->hasOne('User', array('local' => 'user_id',
                                    'foreign' => 'id',
                                    'onDelete' => 'CASCADE'));
    }
    public function setTableDefinition(): void
    {
        $this->hasColumn('id', 'integer', 8, array('autoincrement' => true, 'primary' => true));
        $this->hasColumn('user_id', 'integer', 8);
        $this->hasColumn('name', 'string',20);
    }
}
