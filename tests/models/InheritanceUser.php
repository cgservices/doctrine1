<?php
class InheritanceUser extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->setTableName('inheritance_user');

        $this->hasColumn('id', 'integer', 8, array (  'primary' => true,  'autoincrement' => true,));
        $this->hasColumn('username', 'string', 128, array (  'notnull' => true,));
    }

    public function setUp(): void

    {
        $this->hasMany('InheritanceDeal as Deals', array('refClass' => 'InheritanceDealUser', 'local' => 'user_id', 'foreign' => 'entity_id'));
    }
}