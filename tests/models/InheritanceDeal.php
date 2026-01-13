<?php
class InheritanceDeal extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->setTableName('inheritance_deal');
        
        $this->hasColumn('id', 'integer', 8, array (  'primary' => true,  'autoincrement' => true,));
        $this->hasColumn('name', 'string', 255, array ());
    }
  
    public function setUp(): void
  
    {
        $this->hasMany('InheritanceUser as Users', array('refClass' => 'InheritanceDealUser', 'local' => 'entity_id', 'foreign' => 'user_id'));
    }
}