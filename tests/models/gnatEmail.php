<?php
class gnatEmail extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('address', 'string', 150);
    }
    
    
}
