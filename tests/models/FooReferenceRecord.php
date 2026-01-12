<?php
class FooReferenceRecord extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->setTableName('foo_reference');
        
        $this->hasColumn('foo1', 'integer', null, array('primary' => true));
        $this->hasColumn('foo2', 'integer', null, array('primary' => true));
    }
}
