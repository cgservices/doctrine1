<?php
class EntityAddress extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('user_id', 'integer', null, array('primary' => true));
        $this->hasColumn('address_id', 'integer', null, array('primary' => true));
    }
}
