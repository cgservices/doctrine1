<?php
class PolicyAsset extends Doctrine_Record 
{
    public function setTableDefinition(): void
    {
        $this->hasColumn('policy_number', 'integer', 11);
        $this->hasColumn('value', 'float', 10, array ('notblank' => true,));
    }

    public function setUp(): void

    {
        $this->hasOne('Policy', array('foreign' => 'policy_number', 
                                      'local' => 'policy_number'));
        $this->index('policy_number_index', array('fields' => array('policy_number')));
    }
}
