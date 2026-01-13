<?php

/**
 * Tests in this file are unsorted, and should be placed in an appropriate
 * test file.  If you are unsure where to put a unit test, place them in here
 * and hopefully someone else will move them.
 */
class UnsortedTestCase extends Doctrine_UnitTestCase {

  public function prepareTables()
  {
      $this->tables = array('Package', 'PackageVersion', 'PackageVersionNotes');
      parent::prepareTables();
  }

  public function prepareData()
  {
      // No initial data needed
  }

  public function testCascadingInsert()
  {
      $package = new Package();
      $package->description = 'Package';

      $packageverison = new PackageVersion();
      $packageverison->description = 'Version';

      $packageverisonnotes = new PackageVersionNotes();
      $packageverisonnotes->description = 'Notes';

      $package->Version[0] = $packageverison;
      $package->Version[0]->Note[0] = $packageverisonnotes;

      $package->save();

      $this->assertNotNull($package->id);
      $this->assertNotNull($package->Version[0]->id);
      $this->assertNotNull($package->Version[0]->Note[0]->id);
  }
}
