<?php

$startTime = time();

// Debug Diagnosic process attacher sleep time needed to link process
// More info about that: http://bugs.php.net/bugs-generating-backtrace-win32.php
//sleep(10);

// PHP 8.4: E_STRICT is deprecated and included in E_ALL
error_reporting(E_ALL);
ini_set('max_execution_time', 900);
ini_set('date.timezone', 'GMT+0');

define('DOCTRINE_DIR', $_SERVER['DOCTRINE_DIR']);

require_once(DOCTRINE_DIR . '/lib/Doctrine.php');

spl_autoload_register(array('Doctrine', 'autoload'));
spl_autoload_register(array('Doctrine', 'modelsAutoload'));

require_once(DOCTRINE_DIR . '/tests/DoctrineTest.php');

spl_autoload_register(array('DoctrineTest', 'autoload'));

// Set up the main database connection
// Use MySQL if DOCTRINE_TEST_DSN is set, otherwise fall back to SQLite
if (getenv('DOCTRINE_TEST_DSN')) {
    $dsn = getenv('DOCTRINE_TEST_DSN');
    // Parse mysql://user:pass@host:port/dbname format
    $parts = parse_url($dsn);
    $driver = $parts['scheme'] ?? 'mysql';
    $host = $parts['host'] ?? 'localhost';
    $port = $parts['port'] ?? 3306;
    $user = $parts['user'] ?? 'root';
    $pass = $parts['pass'] ?? '';
    $dbname = ltrim($parts['path'] ?? '/doctrine_tests', '/');

    $pdoDsn = "{$driver}:host={$host};port={$port};dbname={$dbname}";

    try {
        $dbh = new PDO($pdoDsn, $user, $pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Disable only_full_group_by for MySQL 8 compatibility with legacy queries
        // This allows SELECT columns that are not in GROUP BY clause
        $dbh->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        Doctrine_Manager::connection($dbh, 'main');
        define('DOCTRINE_TEST_DRIVER', $driver);
    } catch (PDOException $e) {
        echo "Failed to connect to MySQL: " . $e->getMessage() . "\n";
        echo "Falling back to SQLite in-memory database\n";
        $dbh = new PDO('sqlite::memory:');
        $dbh->sqliteCreateFunction('trim', 'trim', 1);
        Doctrine_Manager::connection($dbh, 'main');
        define('DOCTRINE_TEST_DRIVER', 'sqlite');
    }
} else {
    // Default to SQLite for backward compatibility
    $dbh = new PDO('sqlite::memory:');
    $dbh->sqliteCreateFunction('trim', 'trim', 1);
    Doctrine_Manager::connection($dbh, 'main');
    define('DOCTRINE_TEST_DRIVER', 'sqlite');
}

// Load all model files recursively for PHP 8.4 compatibility
$modelDirs = [
    DOCTRINE_DIR . '/tests/models',
];

foreach ($modelDirs as $dir) {
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                require_once $file->getPathname();
            }
        }
    }
}
