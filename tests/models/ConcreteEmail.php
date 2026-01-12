<?php
class ConcreteEmail extends Doctrine_Record
{
    public function setUp(): void
    {
        $this->actAs('EmailTemplate');
    }
}
