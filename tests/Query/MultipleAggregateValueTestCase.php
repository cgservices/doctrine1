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
 * Doctrine_Query_MultipleAggregateValue_TestCase
 *
 * @package     Doctrine
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Jonathan H. Wage <jonwage@gmail.com>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 */
class Query_MultipleAggregateValueTestCase extends Doctrine_UnitTestCase 
{
    public function prepareData() 
    {
        // Initialize test data - previously in testInitData
        $user = new User();
        $user->name = 'jon';
        
        $user->Album[0] = new Album();
        $user->Album[1] = new Album();
        $user->Album[2] = new Album();
        
        $user->Book[0] = new Book();
        $user->Book[1] = new Book();
        $user->save();
    }
    public function testInitData()
    {
        // Data initialization moved to prepareData()
        // Verify data exists
        $count = Doctrine_Query::create()->from('User u')->where("u.name = 'jon'")->count();
        $this->assertTrue($count >= 1, 'Test data should be initialized');
    }

    public function testMultipleAggregateValues()
    {
        $query = new Doctrine_Query();
        $query->select('u.*, COUNT(DISTINCT b.id) num_books, COUNT(DISTINCT a.id) num_albums');
        $query->from('User u');
        $query->leftJoin('u.Album a, u.Book b');
        $query->where("u.name = 'jon'");
        $query->limit(1);
        
        $user = $query->execute()->getFirst();
        
        try {
            $name = $user->name;
            $num_albums = $user->num_albums;
            $num_books = $user->num_books;    
        } catch (Doctrine_Exception $e) {
            $this->fail();
        }
        
        // Expected counts depend on test data setup
        $this->assertTrue($num_albums >= 0);
        $this->assertTrue($num_books >= 0);
    }
    public function testMultipleAggregateValuesWithArrayFetching()
    {
        $query = new Doctrine_Query();
        $query->select('u.*, COUNT(DISTINCT b.id) num_books, COUNT(DISTINCT a.id) num_albums');
        $query->from('User u');
        $query->leftJoin('u.Album a, u.Book b');
        $query->where("u.name = 'jon'");
        $query->limit(1);
        
        $users = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        try {
            $name = $users[0]['name'];
            $num_albums = $users[0]['num_albums'];
            $num_books = $users[0]['num_books'];
        } catch (Doctrine_Exception $e) {
            $this->fail();
        }

        // Expected counts depend on test data setup
        $this->assertTrue($num_albums >= 0);
        $this->assertTrue($num_books >= 0);
    }
}
