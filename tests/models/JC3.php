<?php
class JC3 extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('c1_id', 'integer', 8);
        $this->hasColumn('c2_id', 'integer', 8);
    }
}

