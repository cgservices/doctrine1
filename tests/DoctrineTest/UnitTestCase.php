<?php
/**
 * UnitTestCase - PHPUnit 11 compatible base class
 *
 * This class bridges the old Doctrine test framework with PHPUnit 11.
 * It extends PHPUnit\Framework\TestCase and provides compatibility methods
 * for the old assertion API.
 */

use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    protected $_passed = 0;
    protected $_failed = 0;
    protected $_messages = array();
    protected $_testCases = array();

    protected static $_passesAndFails = array('passes' => array(), 'fails' => array());
    protected static $_lastRunsPassesAndFails = array('passes' => array(), 'fails' => array());

    /**
     * Initialize the test case
     */
    public function init()
    {
        $tmpFileName = $this->getPassesAndFailsCachePath();

        if (file_exists($tmpFileName)) {
            $array = unserialize(file_get_contents($tmpFileName));
        } else {
            $array = array();
        }
        if ($array) {
            self::$_lastRunsPassesAndFails = $array;
        }
    }

    public function addMessage($msg)
    {
        $this->_messages[] = $msg;
    }

    /**
     * Compatibility method: assertEqual maps to assertEquals
     */
    public function assertEqual($expected, $actual, string $message = ''): void
    {
        $this->assertEquals($expected, $actual, $message);
    }

    /**
     * Compatibility method: assertIdentical maps to assertSame
     */
    public function assertIdentical($expected, $actual, string $message = ''): void
    {
        $this->assertSame($expected, $actual, $message);
    }

    /**
     * Compatibility method: assertNotEqual maps to assertNotEquals
     */
    public function assertNotEqual($expected, $actual, string $message = ''): void
    {
        $this->assertNotEquals($expected, $actual, $message);
    }

    /**
     * Pass tracking (for compatibility)
     */
    public function pass()
    {
        $class = get_class($this);
        if (!isset(self::$_passesAndFails['fails'][$class])) {
            self::$_passesAndFails['passes'][$class] = $class;
        }
        $this->_passed++;
        // In PHPUnit, a test that doesn't fail is considered passed
        $this->assertTrue(true);
    }

    /**
     * Internal fail method for compatibility
     */
    public function _fail($message = "")
    {
        $trace = debug_backtrace();
        array_shift($trace);

        foreach ($trace as $stack) {
            if (substr($stack['function'] ?? '', 0, 4) === 'test') {
                $class = new ReflectionClass($stack['class']);

                if (!isset($line)) {
                    $line = $stack['line'] ?? 0;
                }

                $errorMessage = $class->getName() . ' : method ' . $stack['function'] . ' failed on line ' . $line;
                $this->_messages[] = $errorMessage . " " . $message;
                break;
            }
            $line = $stack['line'] ?? 0;
        }
        $this->_failed++;
        $class = get_class($this);
        if (isset(self::$_passesAndFails['passes'][$class])) {
            unset(self::$_passesAndFails['passes'][$class]);
        }
        self::$_passesAndFails['fails'][$class] = $class;
    }

    public function getMessages()
    {
        return $this->_messages;
    }

    public function getFailCount()
    {
        return $this->_failed;
    }

    public function getPassCount()
    {
        return $this->_passed;
    }

    public function getPassesAndFailsCachePath()
    {
        $dir = dirname(__FILE__) . '/doctrine_tests';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $path = $dir . '/' . md5(serialize(array_keys($this->_testCases)));
        return $path;
    }

    public function cachePassesAndFails()
    {
        $tmpFileName = $this->getPassesAndFailsCachePath();
        file_put_contents($tmpFileName, serialize(self::$_passesAndFails));
    }

    public function getPassesAndFails()
    {
        return self::$_passesAndFails;
    }

    public function getLastRunsPassesAndFails()
    {
        return self::$_lastRunsPassesAndFails;
    }

    public function getLastRunsFails()
    {
        return isset(self::$_lastRunsPassesAndFails['fails']) ? self::$_lastRunsPassesAndFails['fails'] : array();
    }

    public function getLastRunsPass()
    {
        return isset(self::$_lastRunsPassesAndFails['passes']) ? self::$_lastRunsPassesAndFails['passes'] : array();
    }

    public function getNewFails()
    {
        $newFails = array();
        $fails = self::$_passesAndFails['fails'];
        foreach ($fails as $fail) {
            if (isset(self::$_lastRunsPassesAndFails['passes'][$fail])) {
                $newFails[$fail] = $fail;
            }
        }
        return $newFails;
    }

    public function getFixedFails()
    {
        $fixed = array();
        $fails = self::$_lastRunsPassesAndFails['fails'] ?? [];
        foreach ($fails as $fail) {
            if (isset(self::$_passesAndFails['passes'][$fail])) {
                $fixed[$fail] = $fail;
            }
        }
        return $fixed;
    }

    public function getNumNewFails()
    {
        return count($this->getNewFails());
    }

    public function getNumFixedFails()
    {
        return count($this->getFixedFails());
    }
}

