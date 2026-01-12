<?php
class ValidatorTest_DateModel extends Doctrine_Record {
    public function setTableDefinition(): void
    {
        $this->hasColumn('birthday', 'date', null, array('past'));
        $this->hasColumn('death', 'date', null, array('future'));
    }
}
