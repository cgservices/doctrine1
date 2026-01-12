<?php
class ConcreteGroup extends Doctrine_Record
{
    public function setUp(): void
    {
        $this->actAs('GroupTemplate');
    }
}
