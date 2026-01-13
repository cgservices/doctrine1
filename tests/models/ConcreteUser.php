<?php
class ConcreteUser extends Doctrine_Record
{
    public function setUp(): void
    {
        $this->actAs('UserTemplate');
    }
}

