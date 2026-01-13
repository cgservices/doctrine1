<?php
class Account extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('entity_id', 'integer', 8);
        $this->hasColumn('amount', 'integer', 8);
    }
}

