<?php
class FieldNameTest extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('someColumn', 'string', 200, array('default' => 'some string'));
        $this->hasColumn('someEnum', 'enum', 4, array('default' => 'php', 'values' => array('php', 'java', 'python')));
        $this->hasColumn('someArray', 'array', 100);
        $this->hasColumn('someObject', 'object', 200);
        $this->hasColumn('someInt', 'integer', 8, array('default' => 11));
    }
}
