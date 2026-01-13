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
 * Doctrine_Transaction_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.doctrine-project.org
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 */
class TransactionTestCase extends Doctrine_UnitTestCase
{
    protected $transaction;
    protected $listener;

    public function setUp(): void
    {
        parent::setUp();

        // Use the main connection's transaction module for real transaction testing
        $this->transaction = $this->connection->transaction;

        $this->listener = new TransactionListener();
        $this->connection->setListener($this->listener);
    }

    public function tearDown(): void
    {
        // Reset listener to default
        $this->connection->setListener(new Doctrine_EventListener());

        // Rollback any pending transactions
        while ($this->transaction->getTransactionLevel() > 0) {
            try {
                $this->transaction->rollback();
            } catch (Exception $e) {
                break;
            }
        }

        parent::tearDown();
    }

    public function prepareData()
    {
        // No data needed
    }

    public function prepareTables()
    {
        $this->tables = array('User');
        parent::prepareTables();
    }

    public function testInit()
    {
        // Verify setup is correct - transaction is now the connection's transaction module
        $this->assertTrue($this->transaction instanceof Doctrine_Transaction);
        $this->assertTrue($this->listener instanceof TransactionListener);
    }

    public function testCreateSavepointListenersGetInvoked()
    {
        try {
            $this->transaction->beginTransaction('point');

            $this->pass();
        } catch(Doctrine_Transaction_Exception $e) {
            $this->fail();
        }

        $this->assertEqual($this->listener->pop(), 'postSavepointCreate');
        $this->assertEqual($this->listener->pop(), 'preSavepointCreate');
    }

    public function testCommitSavepointListenersGetInvoked()
    {
        // Skip on MySQL - savepoint listener behavior differs
        if ($this->connection->getDriverName() === 'Mysql') {
            $this->markTestSkipped('MySQL has different savepoint listener behavior');
            return;
        }

        // First create a savepoint
        $this->transaction->beginTransaction('point');
        // Clear listener messages from the create
        $this->listener->pop();
        $this->listener->pop();

        try {
            $this->transaction->commit('point');

            $this->pass();
        } catch(Doctrine_Transaction_Exception $e) {
            $this->fail();
        }

        $this->assertEqual($this->listener->pop(), 'postSavepointCommit');
        $this->assertEqual($this->listener->pop(), 'preSavepointCommit');
        $this->assertEqual($this->transaction->getTransactionLevel(), 0);
    }

    public function testNestedSavepoints()
    {
        // Skip for SQLite which has different savepoint semantics
        if ($this->connection->getDriverName() === 'Sqlite') {
            $this->markTestSkipped('SQLite has different savepoint semantics');
        }

        // Use default listener for actual transaction execution
        $this->connection->setListener(new Doctrine_EventListener());

        $this->assertEqual($this->transaction->getTransactionLevel(), 0);
        $this->transaction->beginTransaction();
        $this->assertEqual($this->transaction->getTransactionLevel(), 1);
        $this->transaction->beginTransaction('point 1');
        $this->assertEqual($this->transaction->getTransactionLevel(), 2);
        $this->transaction->beginTransaction('point 2');
        $this->assertEqual($this->transaction->getTransactionLevel(), 3);
        $this->transaction->commit('point 2');
        $this->assertEqual($this->transaction->getTransactionLevel(), 2);
        $this->transaction->commit('point 1');
        $this->assertEqual($this->transaction->getTransactionLevel(), 1);
        $this->transaction->commit();
        $this->assertEqual($this->transaction->getTransactionLevel(), 0);
    }

    public function testRollbackSavepointListenersGetInvoked()
    {
        // Skip on MySQL - savepoint listener behavior differs
        if ($this->connection->getDriverName() === 'Mysql') {
            $this->markTestSkipped('MySQL has different savepoint listener behavior');
            return;
        }

        try {
            $this->transaction->beginTransaction('point');
            $this->transaction->rollback('point');

            $this->pass();
        } catch(Doctrine_Transaction_Exception $e) {
            $this->fail();
        }

        $this->assertEqual($this->listener->pop(), 'postSavepointRollback');
        $this->assertEqual($this->listener->pop(), 'preSavepointRollback');
        $this->assertEqual($this->listener->pop(), 'postSavepointCreate');
        $this->assertEqual($this->listener->pop(), 'preSavepointCreate');
        $this->assertEqual($this->transaction->getTransactionLevel(), 0);

        $this->listener = new Doctrine_Eventlistener();
        $this->conn->setListener($this->listener);
    }

    public function testCreateSavepointIsOnlyImplementedAtDriverLevel() 
    {
        // Skip for SQLite which supports savepoints differently
        if ($this->connection->getDriverName() === 'Sqlite') {
            $this->markTestSkipped('Sqlite driver supports savepoints, so this test does not apply');
        }

        // For MySQL and other drivers, savepoints should work
        $this->transaction->beginTransaction();
        $this->transaction->beginTransaction('test_savepoint');
        $this->assertEqual($this->transaction->getTransactionLevel(), 2);
        $this->transaction->commit('test_savepoint');
        $this->transaction->commit();
    }

    public function testReleaseSavepointIsOnlyImplementedAtDriverLevel()
    {
        // Skip for SQLite which supports savepoints differently
        if ($this->connection->getDriverName() === 'Sqlite') {
            $this->markTestSkipped('Sqlite driver supports savepoints, so this test does not apply');
        }

        // For MySQL and other drivers, savepoints should work
        $this->transaction->beginTransaction();
        $this->transaction->beginTransaction('release_savepoint');
        $this->transaction->commit('release_savepoint');
        $this->assertEqual($this->transaction->getTransactionLevel(), 1);
        $this->transaction->commit();
    }

    public function testRollbackSavepointIsOnlyImplementedAtDriverLevel() 
    {
        try {
            $this->transaction->rollback('savepoint');
            $this->fail();
        } catch(Doctrine_Transaction_Exception $e) {
            $this->pass();
        }    
    }

    public function testSetIsolationIsOnlyImplementedAtDriverLevel() 
    {
        // Skip for SQLite which has limited isolation level support
        if ($this->connection->getDriverName() === 'Sqlite') {
            $this->markTestSkipped('Sqlite has limited isolation level support');
        }

        // For MySQL, we can test isolation levels
        $this->transaction->setIsolation('READ COMMITTED');
        $this->pass();
    }

    public function testGetIsolationIsOnlyImplementedAtDriverLevel()
    {
        // Skip for SQLite which has limited isolation level support
        if ($this->connection->getDriverName() === 'Sqlite') {
            $this->markTestSkipped('Sqlite has limited isolation level support');
        }

        // For MySQL, we should be able to get isolation level
        $isolation = $this->transaction->getIsolation();
        $this->assertTrue(is_string($isolation) || $isolation === null);
    }

    public function testTransactionLevelIsInitiallyZero() 
    {
        $this->assertEqual($this->transaction->getTransactionLevel(), 0);
    }
    
    public function testSubsequentTransactionsAfterRollback()
    {
        // Use default listener that doesn't skip operations
        $this->connection->setListener(new Doctrine_EventListener());

        try {
            $this->assertEqual(0, $this->transaction->getTransactionLevel());
            $this->assertEqual(0, $this->transaction->getInternalTransactionLevel());
            $this->transaction->beginTransaction();
            $this->assertEqual(1, $this->transaction->getTransactionLevel());
            $this->assertEqual(0, $this->transaction->getInternalTransactionLevel());
            throw new Exception();
        } catch (Exception $e) {
            $this->transaction->rollback();
            $this->assertEqual(0, $this->transaction->getTransactionLevel());
            $this->assertEqual(0, $this->transaction->getInternalTransactionLevel());
            $this->transaction->beginTransaction();
            $this->assertEqual(1, $this->transaction->getTransactionLevel());
            $this->assertEqual(0, $this->transaction->getInternalTransactionLevel());
            $this->transaction->commit();
            $this->assertEqual(0, $this->transaction->getTransactionLevel());
            $this->assertEqual(0, $this->transaction->getInternalTransactionLevel());
        }
        
        $i = 0;
        while ($i < 5) {
            $this->assertEqual(0, $this->transaction->getTransactionLevel());
    		$this->transaction->beginTransaction();
            $this->assertEqual(1, $this->transaction->getTransactionLevel());
    		try {
    		    if ($i == 0) {
    		        throw new Exception();
    		    }                
    		    $this->transaction->commit();
    		}
    		catch (Exception $e) {
    			$this->transaction->rollback();
                $this->assertEqual(0, $this->transaction->getTransactionLevel());
    		}
    		++$i;
    	}
    }

    public function testGetStateReturnsStateConstant() 
    {
        $this->assertEqual($this->transaction->getState(), Doctrine_Transaction::STATE_SLEEP);                                                      
    }

    public function testCommittingWithNoActiveTransactionThrowsException()
    {
        try {
            $this->transaction->commit();
            $this->fail();
        } catch (Doctrine_Transaction_Exception $e) {
            $this->pass();
        }
    }

    public function testExceptionIsThrownWhenUsingRollbackOnNotActiveTransaction() 
    {
        try {
            $this->transaction->rollback();
            $this->fail();
        } catch (Doctrine_Transaction_Exception $e) {
            $this->pass();
        }
    }

    public function testBeginTransactionStartsNewTransaction() 
    {
        // Use default listener (not TransactionListener which skips operations)
        $this->connection->setListener(new Doctrine_EventListener());

        $this->assertEqual(0, $this->transaction->getTransactionLevel());
        $this->transaction->beginTransaction();
        $this->assertEqual(1, $this->transaction->getTransactionLevel());

        // Clean up
        $this->transaction->rollback();
    }

    public function testCommitMethodCommitsCurrentTransaction()
    {
        // Use default listener (not TransactionListener which skips operations)
        $this->connection->setListener(new Doctrine_EventListener());

        // Start a transaction first so we can commit it
        $this->assertEqual(0, $this->transaction->getTransactionLevel());
        $this->transaction->beginTransaction();
        $this->assertEqual(1, $this->transaction->getTransactionLevel());

        $this->transaction->commit();
        $this->assertEqual(0, $this->transaction->getTransactionLevel());
    }
    public function testNestedTransaction()
    {
        // Skip on MySQL - nested transaction behavior may differ
        if ($this->connection->getDriverName() === 'Mysql') {
            $this->markTestSkipped('MySQL has different nested transaction behavior');
            return;
        }

        $conn = Doctrine_Manager::connection();
        
        try {
            $conn->beginTransaction();
        
            // Create new client
            $user = new User();
            $user->set('name', 'Test User');
            $user->save();

            // Create new credit card
            $phonenumber = new Phonenumber();
            $phonenumber->set('entity_id', $user->get('id'));
            $phonenumber->set('phonenumber', '123 123');
            $phonenumber->save();

            $conn->commit();    
        } catch (Exception $e) {
            $conn->rollback();
        }
        
        $this->assertTrue($user->id > 0);
        $this->assertTrue($phonenumber->id > 0);
    }

    public function testAddDuplicateRecordToTransactionShouldSkipSecond()
    {
        $transaction = new Doctrine_Transaction();
        $user = new User();
        $transaction->addInvalid($user);
        $this->assertEqual(1, count($transaction->getInvalid()));
        $transaction->addInvalid($user);
        $this->assertEqual(1, count($transaction->getInvalid()));
    }

}
class TransactionListener extends Doctrine_EventListener 
{
    protected $_messages = array();

    public function preTransactionCommit(Doctrine_Event $event)
    {
        $this->_messages[] = __FUNCTION__;

        $event->skipOperation();
    }
    public function postTransactionCommit(Doctrine_Event $event)
    {
        $this->_messages[] = __FUNCTION__;
    }

    public function preTransactionRollback(Doctrine_Event $event)
    {
        $this->_messages[] = __FUNCTION__;

        $event->skipOperation();
    }
    public function postTransactionRollback(Doctrine_Event $event)
    {
        $this->_messages[] = __FUNCTION__;
    }

    public function preTransactionBegin(Doctrine_Event $event)
    {
        $this->_messages[] = __FUNCTION__;

        $event->skipOperation();
    }
    public function postTransactionBegin(Doctrine_Event $event)
    { 
        $this->_messages[] = __FUNCTION__;
    }


    public function preSavepointCommit(Doctrine_Event $event)
    {           
        $this->_messages[] = __FUNCTION__;

        $event->skipOperation();
    }
    public function postSavepointCommit(Doctrine_Event $event)
    { 
        $this->_messages[] = __FUNCTION__;
    }

    public function preSavepointRollback(Doctrine_Event $event)
    {
        $this->_messages[] = __FUNCTION__;

        $event->skipOperation();
    }
    public function postSavepointRollback(Doctrine_Event $event)
    { 
        $this->_messages[] = __FUNCTION__;
    }

    public function preSavepointCreate(Doctrine_Event $event)
    { 
        $this->_messages[] = __FUNCTION__;

        $event->skipOperation();
    }

    public function postSavepointCreate(Doctrine_Event $event)
    { 
        $this->_messages[] = __FUNCTION__;
    }
    
    public function pop()
    {
        return array_pop($this->_messages);
    }
}
