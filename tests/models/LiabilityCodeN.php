<?php
class LiabilityCodeN extends Doctrine_Record
{
  public function setTableDefinition(): void
  {
    $this->setTableName('liability_codes');
    $this->hasColumn('id', 'integer', 8, array('notnull' => true, 'primary' => true, 'autoincrement' => true));
    $this->hasColumn('code', 'integer', 8, array (  'notnull' => true,  'notblank' => true,));
    $this->hasColumn('description', 'string', 4000, array (  'notnull' => true,  'notblank' => true,));
  }
}