<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', '0');

/*
 * WAMP's PHP 5.6.40 installation has mysqli, but does not include php_mysql.dll.
 * These small aliases let the required mysql_* calls below run on that WAMP
 * setup. If the real mysql extension is installed, PHP uses it instead.
 */
if (!function_exists('mysql_connect') && function_exists('mysqli_connect')) {
    if (!defined('MYSQL_ASSOC')) define('MYSQL_ASSOC', MYSQLI_ASSOC);

    function mysql_connect($host, $user, $password)
    {
        $port = 3306;

        if (preg_match('/^(.+):(\d+)$/', $host, $matches)) {
            $host = $matches[1];
            $port = (int)$matches[2];
        }

        return mysqli_connect($host, $user, $password, '', $port);
    }

    function mysql_select_db($database, $connection)
    {
        return mysqli_select_db($connection, $database);
    }

    function mysql_query($sql, $connection = null)
    {
        global $db;

        if ($connection === null) {
            $connection = $db;
        }

        return mysqli_query($connection, $sql);
    }

    function mysql_fetch_array($result, $mode = MYSQLI_BOTH)
    {
        return mysqli_fetch_array($result, $mode);
    }

    function mysql_set_charset($charset, $connection)
    {
        return mysqli_set_charset($connection, $charset);
    }

    function mysql_real_escape_string($value, $connection = null)
    {
        global $db;

        if ($connection === null) {
            $connection = $db;
        }

        return mysqli_real_escape_string($connection, $value);
    }

    function mysql_error($connection = null)
    {
        global $db;

        if ($connection === null) {
            $connection = $db;
        }

        return mysqli_error($connection);
    }
}

if (!function_exists('mysql_connect')) {
    http_response_code(503);
    exit('Database functions are unavailable. Enable mysqli or install PHP 5.6 with the legacy mysql extension.');
}
$config = require __DIR__ . '/config.example.php';
if (file_exists(__DIR__ . '/config.local.php')) {
    $config = require __DIR__ . '/config.local.php';
}

$db = @mysql_connect(
    $config['host'],
    $config['user'],
    $config['password']
);

if (!$db || !@mysql_select_db($config['database'], $db)) {
    http_response_code(503);
    exit('Database connection unavailable. Check config.local.php and import database.sql.');
}

mysql_set_charset('utf8mb4', $db);

function query($sql)
{
    global $db;
    $result = mysql_query($sql, $db);

    if ($result === false) {
        error_log('Database error: ' . mysql_error($db));
        throw new Exception('The database operation failed. Please try again.');
    }

    return $result;
}

function rows($sql)
{
    $result = query($sql);
    $rows = array();

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $rows[] = $row;
    }

    return $rows;
}

function one($sql)
{
    $resultRows = rows($sql);

    return $resultRows ? $resultRows[0] : null;
}

function sql_string($value)
{
    global $db;

    return "'" . mysql_real_escape_string($value, $db) . "'";
}
