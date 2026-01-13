<?php
class ValidatorTest_ClientModel extends Doctrine_Record {
	public function setTableDefinition(): void
	{
		$this->hasColumn('id', 'integer', 8, array(
			'notnull' => true,
			'primary' => true,
			'autoincrement' => true,
			'unsigned' => true
		));
		$this->hasColumn('short_name', 'string', 32, array('notnull' => true, 'notblank', 'unique' => true));
	}

	public function setUp(): void

	{
		$this->hasMany("ValidatorTest_AddressModel", array('local' => 'client_id', 'foreign' => 'address_id', 'refClass' => 'ValidatorTest_ClientToAddressModel'));
	}
}
