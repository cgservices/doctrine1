<?php
class SearchTest extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('title', 'string', 100);
        $this->hasColumn('content', 'string');
    }
    public function setUp(): void
    {
    	$options = array('generateFiles' => false,
                         'fields' => array('title', 'content'));

        $this->actAs('Searchable', $options);
    }
}
