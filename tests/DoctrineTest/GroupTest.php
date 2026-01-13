<?php
/**
 * GroupTest - Legacy test group runner
 *
 * Note: This class is kept for backward compatibility but is not used by PHPUnit 11.
 * PHPUnit discovers and runs tests automatically.
 * This class does NOT extend UnitTestCase because it's a test runner, not a test case.
 */
class GroupTest
{
    protected $_testCases = array();
    protected $_name;
    protected $_title;
    protected $_onlyRunFailed = false;
    protected $_formatter;
    protected $_passed = 0;
    protected $_failed = 0;
    protected $_messages = array();

    protected static $_passesAndFails = array('passes' => array(), 'fails' => array());
    protected static $_lastRunsPassesAndFails = array('passes' => array(), 'fails' => array());

    public function __construct(string $title = '', string $name = '')
    {
        $this->_title = $title;
        $this->_name = $name;
        if (PHP_SAPI != 'cli' && !defined('STDOUT')) {
            define('STDOUT', '');
        }
        if (class_exists('Doctrine_Cli_AnsiColorFormatter')) {
            $this->_formatter = new Doctrine_Cli_AnsiColorFormatter();
        }
    }

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

    public function onlyRunFailed($bool)
    {
        $this->_onlyRunFailed = $bool;
    }

    public function getName(): string
    {
        return $this->_name;
    }

    public function addTestCase(UnitTestCase $testCase)
    {
        if ($testCase instanceof GroupTest) {
            $this->_testCases = array_merge($this->_testCases, $testCase->getTestCases());
        } else {
            $this->_testCases[get_class($testCase)] = $testCase;
        }
    }

    public function addMessage($msg)
    {
        $this->_messages[] = $msg;
    }

    public function shouldBeRun($testCase, $filter)
    {
        if (!is_array($filter)) {
            return true;
        }
        foreach ($filter as $subFilter) {
            $name = strtolower(get_class($testCase));
            $pos = strpos($name, strtolower($subFilter));
            if ($pos === false) {
                return false;
            }
        }
        return true;
    }

    public function run(?DoctrineTest_Reporter $reporter = null, $filter = null): bool
    {
        set_time_limit(900);

        $this->init();

        if ($reporter) {
            $reporter->setTestCase($this);
            $reporter->paintHeader($this->_title);
        }

        $lastRunsFails = $this->getLastRunsFails();

        foreach ($this->_testCases as $k => $testCase) {
            if ($this->_onlyRunFailed && !isset($lastRunsFails[get_class($testCase)])) {
                continue;
            }

            if ($reporter) {
                $reporter->setTestCase($testCase);
            }

            if (!$this->shouldBeRun($testCase, $filter)) {
                continue;
            }
            try {
                $testCase->run();
            } catch (Exception $e) {
                $this->_failed += 1;
                $message = 'Unexpected ' . get_class($e) . ' thrown in [' . get_class($testCase) . '] with message [' . $e->getMessage() . '] in ' . $e->getFile() . ' on line ' . $e->getLine() . "\n\nTrace\n-------------\n\n" . $e->getTraceAsString();
                $testCase->addMessage($message);
            }

            $this->_passed += $testCase->getPassCount();
            $this->_failed += $testCase->getFailCount();

            $this->_testCases[$k] = null;

            if ($reporter) {
                $reporter->paintMessages();
            }
        }

        if ($reporter) {
            $reporter->setTestCase($this);
            $reporter->paintMessages();
        }

        $this->cachePassesAndFails();

        if ($reporter) {
            $reporter->paintFooter();
        }

        return $this->_failed ? false : true;
    }

    public function getTestCaseCount()
    {
        return count($this->_testCases);
    }

    public function getTestCases()
    {
        return $this->_testCases;
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

