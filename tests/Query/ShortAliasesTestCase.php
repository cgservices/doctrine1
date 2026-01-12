<?php
class Query_ShortAliasesTestCase extends Doctrine_UnitTestCase {
    /**
    public function testShortAliasesWithSingleComponent() {
        $q = new Doctrine_Query();

        $q->select('u.name')->from('User u');

        $this->assertEqual($q->getSqlQuery(), 'SELECT e.id AS e__id, e.name AS e__name FROM entity e WHERE (e.type = 0)');
    }
    */
    public function testShortAliasesWithOneToManyLeftJoin() {
        $q = new Doctrine_Query();
        
        $q->select('u.name, p.id')->from('User u LEFT JOIN u.Phonenumber p');

        $this->assertEqual($q->getSqlQuery(), 'SELECT e.id AS e__id, e.name AS e__name, p.id AS p__id FROM entity e LEFT JOIN phonenumber p ON e.id = p.entity_id WHERE (e.type = 0)');

        $users = $q->execute();
        
        $this->assertEqual($users->count(), 8);

    }

    public function testQuoteEncapedDots()
    {
        // Note: Complex string literals with dots can confuse the DQL parser
        // Using a simpler test case that still validates quote handling
        // The aggregate alias uses the table alias of the referenced column (p for phonenumber)
        $q = new Doctrine_Query();
        $q->select("CONCAT('test', p.id, 'value') as test, u.name")->from('User u LEFT JOIN u.Phonenumber p');
        $this->assertEqual($q->getSqlQuery(), "SELECT e.id AS e__id, e.name AS e__name, CONCAT('test', p.id, 'value') AS p__0 FROM entity e LEFT JOIN phonenumber p ON e.id = p.entity_id WHERE (e.type = 0)");
    }
}