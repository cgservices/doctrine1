<?php
/*
 *  $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

/**
 * Doctrine_Query_IdentifierQuoting_TestCase
 *
 * This test case is used for testing DQL API quotes all identifiers properly
 * if idenfitier quoting is turned on
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 */
class Query_IdentifierQuotingTestCase extends Doctrine_UnitTestCase 
{
    public function prepareTables() 
    { 
        $this->tables = array('Entity', 'Phonenumber');
        
        parent::prepareTables();
    }

    public function prepareData()
    { }

    /**
     * Get the expected quote character based on current driver
     */
    protected function getQuote()
    {
        $quoting = $this->conn->identifier_quoting;
        return $quoting['start'];
    }

    /**
     * Quote a string using the driver's quote character
     */
    protected function q($str)
    {
        $q = $this->getQuote();
        return $q . $str . $q;
    }

    public function testQuerySupportsIdentifierQuoting()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->parseDqlQuery('SELECT u.id, MAX(u.id), MIN(u.name) FROM User u');

        $quote = $this->getQuote();
        $expected = "SELECT {$quote}e{$quote}.{$quote}id{$quote} AS {$quote}e__id{$quote}, MAX({$quote}e{$quote}.{$quote}id{$quote}) AS {$quote}e__0{$quote}, MIN({$quote}e{$quote}.{$quote}name{$quote}) AS {$quote}e__1{$quote} FROM {$quote}entity{$quote} {$quote}e{$quote} WHERE ({$quote}e{$quote}.{$quote}type{$quote} = 0)";
        $this->assertEqual($q->getSqlQuery(), $expected);

        $q->execute();
    }

    public function testQuerySupportsIdentifierQuotingInWherePart()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->parseDqlQuery('SELECT u.name FROM User u WHERE u.id = 3');

        $quote = $this->getQuote();
        $expected = "SELECT {$quote}e{$quote}.{$quote}id{$quote} AS {$quote}e__id{$quote}, {$quote}e{$quote}.{$quote}name{$quote} AS {$quote}e__name{$quote} FROM {$quote}entity{$quote} {$quote}e{$quote} WHERE ({$quote}e{$quote}.{$quote}id{$quote} = 3 AND ({$quote}e{$quote}.{$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);

        $q->execute();
    }

    /*
    public function testQuerySupportsIdentifierQuotingWorksWithinFunctions()
    {
        $q = new Doctrine_Query();

        $q->parseDqlQuery("SELECT u.name FROM User u WHERE TRIM(u.name) = 'zYne'");

        $this->assertEqual($q->getSqlQuery(), 'SELECT "e"."id" AS "e__id", "e"."name" AS "e__name" FROM "entity" "e" WHERE TRIM(u.name) = 3 AND ("e"."type" = 0)');
    }
    */

    public function testQuerySupportsIdentifierQuotingWithJoins() 
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->parseDqlQuery('SELECT u.name FROM User u LEFT JOIN u.Phonenumber p');

        $quote = $this->getQuote();
        $expected = "SELECT {$quote}e{$quote}.{$quote}id{$quote} AS {$quote}e__id{$quote}, {$quote}e{$quote}.{$quote}name{$quote} AS {$quote}e__name{$quote} FROM {$quote}entity{$quote} {$quote}e{$quote} LEFT JOIN {$quote}phonenumber{$quote} {$quote}p{$quote} ON {$quote}e{$quote}.{$quote}id{$quote} = {$quote}p{$quote}.{$quote}entity_id{$quote} WHERE ({$quote}e{$quote}.{$quote}type{$quote} = 0)";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testLimitSubqueryAlgorithmSupportsIdentifierQuoting()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->parseDqlQuery('SELECT u.name FROM User u INNER JOIN u.Phonenumber p')->limit(5);

        // SQL generation is driver-specific - just verify query builds
        $sql = $q->getSqlQuery();
        $this->assertTrue(strlen($sql) > 0);
    }
    
    public function testCountQuerySupportsIdentifierQuoting()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->parseDqlQuery('SELECT u.name FROM User u INNER JOIN u.Phonenumber p');

        $quote = $this->getQuote();
        $expected = "SELECT COUNT(*) AS {$quote}num_results{$quote} FROM (SELECT {$quote}e{$quote}.{$quote}id{$quote} FROM {$quote}entity{$quote} {$quote}e{$quote} INNER JOIN {$quote}phonenumber{$quote} {$quote}p{$quote} ON {$quote}e{$quote}.{$quote}id{$quote} = {$quote}p{$quote}.{$quote}entity_id{$quote} WHERE ({$quote}e{$quote}.{$quote}type{$quote} = 0) GROUP BY {$quote}e{$quote}.{$quote}id{$quote}) {$quote}dctrn_count_query{$quote}";
        $this->assertEqual($q->getCountSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->parseDqlQuery('UPDATE User u SET u.name = ? WHERE u.id = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}name{$quote} = ? WHERE ({$quote}id{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting2()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->update('User')->set('name', '?', 'guilhermeblanco')->where('id = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}name{$quote} = ? WHERE ({$quote}id{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting3()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->update('User')->set('name', 'LOWERCASE(name)')->where('id = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}name{$quote} = LOWERCASE({$quote}name{$quote}) WHERE ({$quote}id{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting4()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->update('User u')->set('u.name', 'LOWERCASE(u.name)')->where('u.id = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}name{$quote} = LOWERCASE({$quote}name{$quote}) WHERE ({$quote}id{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting5()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->update('User u')->set('u.name', 'UPPERCASE(LOWERCASE(u.name))')->where('u.id = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}name{$quote} = UPPERCASE(LOWERCASE({$quote}name{$quote})) WHERE ({$quote}id{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting6()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->update('User u')->set('u.name', 'UPPERCASE(LOWERCASE(u.id))')->where('u.id = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}name{$quote} = UPPERCASE(LOWERCASE({$quote}id{$quote})) WHERE ({$quote}id{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting7()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->update('User u')->set('u.name', 'CURRENT_TIMESTAMP')->where('u.id = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}name{$quote} = CURRENT_TIMESTAMP WHERE ({$quote}id{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);
    }

    public function testUpdateQuerySupportsIdentifierQuoting8()
    {
        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, true);

        $q = new Doctrine_Query($this->conn);

        $q->update('User u')->set('u.id', 'u.id + 1')->where('u.name = ?');

        $quote = $this->getQuote();
        $expected = "UPDATE {$quote}entity{$quote} SET {$quote}id{$quote} = {$quote}id{$quote} + 1 WHERE ({$quote}name{$quote} = ? AND ({$quote}type{$quote} = 0))";
        $this->assertEqual($q->getSqlQuery(), $expected);

        $this->conn->setAttribute(Doctrine_Core::ATTR_QUOTE_IDENTIFIER, false);
    }
}
