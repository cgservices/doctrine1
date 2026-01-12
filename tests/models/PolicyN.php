<?php
class PolicyN extends Doctrine_Record
{
  public function setTableDefinition(): void
  {
    $this->setTableName('policies');
    $this->hasColumn('id', 'integer', 8, array('notnull' => true, 'primary' => true, 'autoincrement' => true));
    $this->hasColumn('rate_id', 'integer', 8, array ( ));
    $this->hasColumn('policy_number', 'integer', 8, array (  'unique' => true, ));
  }
  
  public function setUp(): void
  
  {
    $this->hasOne('RateN', array('local' => 'rate_id', 'foreign' => 'id' ));
  }
}