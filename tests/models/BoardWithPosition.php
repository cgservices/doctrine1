<?php
class BoardWithPosition extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('position', 'integer', 8);
        $this->hasColumn('category_id', 'integer', 8);
    }
    public function setUp(): void
    {
        $this->hasOne('CategoryWithPosition as Category', array('local' => 'category_id', 'foreign' => 'id', 'onDelete' => 'CASCADE'));
    }
}
