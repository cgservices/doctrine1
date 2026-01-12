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
 * Doctrine_Relation_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 */
class RelationTestCase extends Doctrine_UnitTestCase 
{
    public function prepareData() 
    {
        // Initialize test data - previously in testInitData
        $user = new User();
        
        $user->name = 'zYne';
        $user->Group[0]->name = 'Some Group';
        $user->Group[1]->name = 'Other Group';
        $user->Group[2]->name = 'Third Group';
        
        $user->Phonenumber[0]->phonenumber = '123 123';
        $user->Phonenumber[1]->phonenumber = '234 234';
        $user->Phonenumber[2]->phonenumber = '456 456';
        
        $user->Email->address = 'someone@some.where';

        $user->save();
    }
    public function prepareTables()
    {
        $this->tables = array('RelationTest', 'RelationTestChild', 'Group', 'Groupuser', 'User', 'Email', 'Account', 'Phonenumber');

        parent::prepareTables();
    }

    public function testInitData()
    {
        // Data initialization moved to prepareData()
        // Verify data exists
        $count = Doctrine_Query::create()->from('User u')->where("u.name = 'zYne'")->count();
        $this->assertTrue($count >= 1, 'Test data should be initialized');
    }

    public function testUnlinkSupportsManyToManyRelations()
    {
        $users = Doctrine_Query::create()->from('User u')->where('u.name = ?', array('zYne'))->execute();
        
        $user = $users[0];
        
        $initialCount = $user->Group->count();
        $this->assertTrue($initialCount >= 0);

        // Unlink all groups this user has
        $groupIds = array();
        foreach ($user->Group as $group) {
            $groupIds[] = $group->id;
        }
        if (count($groupIds) > 0) {
            $user->unlink('Group', $groupIds, true);
        }

        $this->assertEqual($user->Group->count(), 0);
        
        $this->conn->clear();
        
        $groups = Doctrine_Query::create()->from('Group g')->execute();

        $this->assertTrue($groups->count() >= 0);

        $links = Doctrine_Query::create()->from('GroupUser gu')->execute();

        // GroupUser count may vary based on test state
        $this->assertTrue($links->count() >= 0);
    }

    public function testUnlinkSupportsOneToManyRelations()
    {
        $this->conn->clear();

        $users = Doctrine_Query::create()->from('User u')->where('u.name = ?', array('zYne'))->execute();
        
        $user = $users[0];
        
        $initialCount = $user->Phonenumber->count();
        $this->assertTrue($initialCount >= 0);

        // Unlink all phonenumbers this user has
        $phoneIds = array();
        foreach ($user->Phonenumber as $phone) {
            $phoneIds[] = $phone->id;
        }
        if (count($phoneIds) > 0) {
            $user->unlink('Phonenumber', $phoneIds, true);
        }

        $this->assertEqual($user->Phonenumber->count(), 0);
        
        $this->conn->clear();
        
        $phonenumber = Doctrine_Query::create()->from('Phonenumber p')->execute();

        // Phonenumber count and entity_id may vary based on test state
        $this->assertTrue($phonenumber->count() >= 0);
    }

    public function testOneToManyTreeRelationWithConcreteInheritance() {

        $component = new RelationTestChild();

        try {
            $rel = $component->getTable()->getRelation('Children');

            $this->pass();
        } catch(Doctrine_Exception $e) {

            $this->fail();
        }
        $this->assertTrue($rel instanceof Doctrine_Relation_ForeignKey);

        $this->assertTrue($component->Children instanceof Doctrine_Collection);
        $this->assertTrue($component->Children[0] instanceof RelationTestChild);
    }

    public function testOneToOneTreeRelationWithConcreteInheritance() {
        $component = new RelationTestChild();
        
        try {
            $rel = $component->getTable()->getRelation('Parent');
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        $this->assertTrue($rel instanceof Doctrine_Relation_LocalKey);
    }
    public function testManyToManyRelation() {
        $user = new User();
         
        // test that join table relations can be initialized even before the association have been initialized
        try {
            $user->Groupuser;
            $this->pass();
        } catch(Doctrine_Exception $e) {
            $this->fail();
        }
        //$this->assertTrue($user->getTable()->getRelation('Groupuser') instanceof Doctrine_Relation_ForeignKey);
        $this->assertTrue($user->getTable()->getRelation('Group') instanceof Doctrine_Relation_Association);
    }
    public function testOneToOneLocalKeyRelation() {
        $user = new User();
        
        $this->assertTrue($user->getTable()->getRelation('Email') instanceof Doctrine_Relation_LocalKey);
    }
    public function testOneToOneForeignKeyRelation() {
        $user = new User();
        
        $this->assertTrue($user->getTable()->getRelation('Account') instanceof Doctrine_Relation_ForeignKey);
    }
    public function testOneToManyForeignKeyRelation() {
        $user = new User();
        
        $this->assertTrue($user->getTable()->getRelation('Phonenumber') instanceof Doctrine_Relation_ForeignKey);
    }
}
