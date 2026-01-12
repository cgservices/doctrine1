<?php
class ORM_TestEntry extends Doctrine_Record {
   public function setTableDefinition(): void
   {
        $this->setTableName('test_entries');
        $this->hasColumn('id', 'integer', 11, 'autoincrement|primary');
        $this->hasColumn('name', 'string', 255); 
        $this->hasColumn('stamp', 'timestamp');
        $this->hasColumn('amount', 'float'); 
        $this->hasColumn('itemID', 'integer', 8); 
   } 
    
   public function setUp(): void
    
   {  
        $this->hasOne('ORM_TestItem', array(
            'local' => 'itemID', 'foreign' => 'id'
        ));
   }
}
