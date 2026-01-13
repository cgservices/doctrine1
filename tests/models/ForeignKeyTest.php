<?php
class ForeignKeyTest extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', null);
        $this->hasColumn('code', 'integer', 8);
        $this->hasColumn('content', 'string', 4000);
        $this->hasColumn('parent_id', 'integer', 8);

        $this->hasOne('ForeignKeyTest as Parent',
                       array('local'    => 'parent_id',
                             'foreign'  => 'id',
                             'onDelete' => 'CASCADE',
                             'onUpdate' => 'RESTRICT')
                       );

        $this->hasMany('ForeignKeyTest as Children', array(
                'local' => 'id', 'foreign' => 'parent_id', 'cascade' => array('delete')));

        $this->option('type', 'INNODB');

    }
}
