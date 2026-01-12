<?php

class Doctrine_Template_TestBehavior extends Doctrine_Template
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('test', 'string', 255);
    }
}