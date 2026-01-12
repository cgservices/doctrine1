<?php
class I18nTest extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 200);
        $this->hasColumn('title', 'string', 200);
    }
    public function setUp(): void
    {
        $this->actAs('I18n', array('fields' => array('name', 'title')));
    }
}
