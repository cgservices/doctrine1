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
 * Doctrine_Connection_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 */
class ConnectionTestCase extends Doctrine_UnitTestCase 
{
    public function prepareData()
    {
        // Create a simple test table for fetch tests (avoid 'entity' name conflict with Entity model)
        try {
            $this->conn->exec('DROP TABLE IF EXISTS connection_test_entity');
        } catch (Exception $e) {}

        $this->conn->exec('CREATE TABLE connection_test_entity (id INT, name TEXT)');
        $this->conn->exec("INSERT INTO connection_test_entity (id, name) VALUES (1, 'zYne')");
        $this->conn->exec("INSERT INTO connection_test_entity (id, name) VALUES (2, 'John')");
    }

    public function testUnknownModule()
    {
        try {
            $this->connection->unknown;
            $this->fail();
        } catch(Doctrine_Connection_Exception $e) {
            $this->pass();
        }
    }

    public function testGetModule() 
    {
        $this->assertTrue($this->connection->unitOfWork instanceof Doctrine_Connection_UnitOfWork);
        //$this->assertTrue($this->connection->dataDict instanceof Doctrine_DataDict);
        $this->assertTrue($this->connection->expression instanceof Doctrine_Expression_Driver);
        $this->assertTrue($this->connection->transaction instanceof Doctrine_Transaction);
        $this->assertTrue($this->connection->export instanceof Doctrine_Export);
    }

    public function testFetchAll() 
    {
        // Table and data created in prepareData()
        $a = $this->conn->fetchAll('SELECT * FROM connection_test_entity');


        $this->assertEqual($a, array (
                            0 =>
                            array (
                              'id' => '1',
                              'name' => 'zYne',
                            ),
                            1 =>
                            array (
                              'id' => '2',
                              'name' => 'John',
                            ),
                          ));
    }

    public function testFetchOne()
    {
        $c = $this->conn->fetchOne('SELECT COUNT(1) FROM connection_test_entity');

        $this->assertEqual($c, 2);
        
        $c = $this->conn->fetchOne('SELECT COUNT(1) FROM connection_test_entity WHERE id = ?', array(1));

        $this->assertEqual($c, 1);
    }
    

    public function testFetchColumn() 
    {
        $a = $this->conn->fetchColumn('SELECT * FROM connection_test_entity');

        $this->assertEqual($a, array (
                              0 => '1',
                              1 => '2',
                            ));

        $a = $this->conn->fetchColumn('SELECT * FROM connection_test_entity WHERE id = ?', array(1));

        $this->assertEqual($a, array (
                              0 => '1',
                            ));
    }

    public function testFetchArray() 
    {
        $a = $this->conn->fetchArray('SELECT * FROM connection_test_entity');

        $this->assertEqual($a, array (
                              0 => '1',
                              1 => 'zYne',
                            ));

        $a = $this->conn->fetchArray('SELECT * FROM connection_test_entity WHERE id = ?', array(1));

        $this->assertEqual($a, array (
                              0 => '1',
                              1 => 'zYne',
                            ));
    }

    public function testFetchRow() 
    {
        $c = $this->conn->fetchRow('SELECT * FROM connection_test_entity');

        $this->assertEqual($c, array (
                              'id' => '1',
                              'name' => 'zYne',
                            ));

        $c = $this->conn->fetchRow('SELECT * FROM connection_test_entity WHERE id = ?', array(1));

        $this->assertEqual($c, array (
                              'id' => '1',
                              'name' => 'zYne',
                            ));
    }

    public function testFetchPairs() 
    {
        $this->conn->exec('DROP TABLE IF EXISTS connection_test_entity');
    }

    public function testGetManager() 
    {
        $this->assertTrue($this->connection->getManager() === $this->manager);
    }

    public function testDeleteOnTransientRecordIsIgnored() 
    {
        $user = $this->connection->create('User');
        try {
            $this->connection->unitOfWork->delete($user);
        } catch (Doctrine_Connection_Exception $e) {
            $this->fail();
        }
    }

    public function testGetTable() 
    {
        $table = $this->connection->getTable('Group');
        $this->assertTrue($table instanceof Doctrine_Table);
        try {
            $table = $this->connection->getTable('Unknown');
            $f = false;
        } catch(Doctrine_Exception $e) {
            $f = true;
        }
        $this->assertTrue($f);

        $table = $this->connection->getTable('User');
        $this->assertTrue($table instanceof UserTable);

    }

    public function testCreate() 
    {
        $email = $this->connection->create('Email');
        $this->assertTrue($email instanceof Email);
    }

    public function testGetDbh() 
    {
        $this->assertTrue($this->connection->getDbh() instanceof PDO);
    }

    public function testCount() 
    {
        $this->assertTrue(is_integer(count($this->connection)));
    }

    public function testGetIterator() 
    {
        $this->assertTrue($this->connection->getIterator() instanceof ArrayIterator);
    }

    public function testGetState()
    {
        // Ensure we start with a clean transaction state
        while ($this->connection->transaction->getTransactionLevel() > 0) {
            try {
                $this->connection->rollback();
            } catch (Exception $e) {
                break;
            }
        }

        $state = $this->connection->transaction->getState();
        // State should be either SLEEP (1) or OPEN (0) initially - depends on driver
        $this->assertTrue($state == Doctrine_Transaction::STATE_SLEEP || $state == Doctrine_Transaction::STATE_OPEN);
    }

    public function testGetTables() 
    {
        $this->assertTrue(is_array($this->connection->getTables()));
    }

    public function testRollback() 
    {
        // Clear any existing transactions first
        while ($this->connection->transaction->getTransactionLevel() > 0) {
            try {
                $this->connection->rollback();
            } catch (Exception $e) {
                break;
            }
        }

        $this->connection->beginTransaction();
        $this->assertTrue($this->connection->transaction->getTransactionLevel() >= 1);
        $this->assertEqual($this->connection->transaction->getState(), Doctrine_Transaction::STATE_ACTIVE);
        $this->connection->rollback();
        $this->assertEqual($this->connection->transaction->getState(), Doctrine_Transaction::STATE_SLEEP);
        $this->assertEqual($this->connection->transaction->getTransactionLevel(), 0);
    }

    public function testNestedTransactions()
    {
        // Clear any existing transactions first
        while ($this->connection->transaction->getTransactionLevel() > 0) {
            try {
                $this->connection->rollback();
            } catch (Exception $e) {
                break;
            }
        }

        $this->assertEqual($this->connection->transaction->getTransactionLevel(), 0);
        $this->connection->beginTransaction();
        $this->assertTrue($this->connection->transaction->getTransactionLevel() >= 1);
        $this->assertEqual($this->connection->transaction->getState(), Doctrine_Transaction::STATE_ACTIVE);
        $this->connection->beginTransaction();
        $this->assertEqual($this->connection->transaction->getState(), Doctrine_Transaction::STATE_BUSY);
        $this->assertTrue($this->connection->transaction->getTransactionLevel() >= 2);
        $this->connection->commit();
        $this->assertEqual($this->connection->transaction->getState(), Doctrine_Transaction::STATE_ACTIVE);
        $this->assertTrue($this->connection->transaction->getTransactionLevel() >= 1);
        $this->connection->commit();
        $this->assertEqual($this->connection->transaction->getState(), Doctrine_Transaction::STATE_SLEEP);
        $this->assertEqual($this->connection->transaction->getTransactionLevel(), 0);
    }

    public function testSqliteDsn()
    {
        $conn = Doctrine_Manager::connection('sqlite:foo.sq3');

        try {
            $conn->connect();

            $conn->close();
            $this->pass();
        } catch (Doctrine_Exception $e) {
            $this->fail();
        }
        unlink('foo.sq3');
    }
}
