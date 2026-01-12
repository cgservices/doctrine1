<?php
class ValidatorTest_ClientToAddressModel extends Doctrine_Record
{
	public function setTableDefinition(): void
	{
		$this->hasColumn("client_id", "integer", 8, array('primary' => true, 'unsigned' => true));
		$this->hasColumn("address_id", "integer", 8, array('primary' => true, 'unsigned' => true));
	}

	public function construct(): void

	{

	}

	public function setUp(): void

	{
		$this->hasOne('ValidatorTest_ClientModel', array('local' => 'client_id', 'foreign' => 'id'));
		$this->hasOne('ValidatorTest_AddressModel', array('local' => 'address_id', 'foreign' => 'id'));
	}
}