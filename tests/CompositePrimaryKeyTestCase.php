<?php
class CompositePrimaryKeyTestCase extends Doctrine_UnitTestCase {
    public function prepareData() { }

    public function prepareTables() { 
        $this->tables = array();
        $this->tables[] = "CPK_Test";
        $this->tables[] = "CPK_Test2";
        $this->tables[] = "CPK_Association";
        
        parent::prepareTables();
    }

    /**
     * Placeholder test - this class is a stub for future tests
     */
    public function testPlaceholder(): void
    {
        $this->assertTrue(true);
    }
}
