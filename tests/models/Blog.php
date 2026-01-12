<?php
class Blog extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
    	
    }
    public function setUp(): void
    {
        $this->actAs('Taggable');
    }
}
class Taggable extends Doctrine_Template
{
    public function setUp(): void
    {
        //$this->hasMany('[Component]TagTemplate as Tag');
    }
}
class TagTemplate extends Doctrine_Record
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('name', 'string', 100);
        $this->hasColumn('description', 'string');
    }

    public function setUp(): void

    {
        //$this->hasOne('[Component]', array('onDelete' => 'CASCADE'));
    }
}
