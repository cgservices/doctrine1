<?php
class Rec2  extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('user_id', 'integer', 10, array (  'unique' => true,));
        $this->hasColumn('address', 'string', 150, array ());
    }

    public function setUp(): void

    {
        $this->hasOne('Rec1 as User', array('local' => 'id', 'foreign' => 'user_id', 'onDelete' => 'CASCADE'));
    }

}
