<?php
class ClientModel extends Doctrine_Record
{
	public function setTableDefinition(): void
	{
		$this->setTableName('clients');

		$this->hasColumn('id', 'integer', 8, array('notnull' => true,
	                                           'primary' => true,
	                                           'autoincrement' => true,
	                                           'unsigned' => true));
		$this->hasColumn('short_name', 'string', 32, array('notnull' => true, 'notblank', 'unique' => true));
	}

	public function setUp(): void

	{
		$this->hasMany('AddressModel', array('local' => 'client_id', 'foreign' => 'address_id', 'refClass' => 'ClientToAddressModel'));
	}
}

class ClientToAddressModel extends Doctrine_Record 
{
	public function setTableDefinition(): void
	{
		$this->setTableName('clients_to_addresses');

		$this->hasColumn('client_id', 'integer', 8, array('primary' => true, 'unsigned' => true));
		$this->hasColumn('address_id', 'integer', 8, array('primary' => true));
	}

	public function construct(): void

	{
	}

	public function setUp(): void

	{
    	$this->hasOne('ClientModel', array('local' => 'client_id', 'foreign' => 'id', 'onDelete' => 'CASCADE'));
    	$this->hasOne('AddressModel', array('local' => 'address_id', 'foreign' => 'id', 'onDelete' => 'CASCADE'));
	}
}

class AddressModel extends Doctrine_Record 
{
	public function setTableDefinition(): void
	{
		$this->setTableName('addresses');

		$this->hasColumn('id', 'integer', 8, array('autoincrement' => true,
													'primary'       => true
													));
		$this->hasColumn('address1', 'string', 255, array('notnull' => true, 'notblank'));
		$this->hasColumn('address2', 'string', 255, array('notnull' => true));
		$this->hasColumn('city', 'string', 255, array('notnull' => true, 'notblank'));
		$this->hasColumn('state', 'string', 10, array('notnull' => true, 'notblank', 'usstate'));
		$this->hasColumn('zip', 'string', 15, array('notnull' => true, 'notblank', 'regexp' => '/^[0-9-]*$/'));
	}

	public function setUp(): void

	{
		$this->hasMany('ClientModel', array('local' => 'address_id', 'foreign' => 'client_id', 'refClass' => 'ClientToAddressModel'));
	}
}
