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
 * Doctrine_OneTableOneClassInheritance_TestCase
 *
 * @package     Doctrine
 * @author      Bjarte Stien Karlsen <bjartka@pvv.ntnu.no> 
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @category    Object Relational Mapping
 * @link        www.doctrine-project.org
 * @since       1.0
 * @version     $Revision$
 */
class OneTableOneClassInheritanceTestCase extends Doctrine_UnitTestCase 
{
    public function prepareData() 
    { }
    public function prepareTables() 
    { }
    public function testTableExporting()
    {
        $sql = $this->conn->export->exportClassesSql(array('ConcreteInheritanceTestParent',
                                                           'ConcreteInheritanceTestChild'));
        // Database-agnostic check - just verify we get the expected number of SQL statements
        $this->assertEqual(count($sql), 2);
        // Verify the table names are in the SQL
        $this->assertTrue(stripos($sql[0], 'concrete_inheritance_test_parent') !== false || stripos($sql[0], 'concrete_inheritance_test_child') !== false);
        $this->assertTrue(stripos($sql[1], 'concrete_inheritance_test_parent') !== false || stripos($sql[1], 'concrete_inheritance_test_child') !== false);
    }
}
