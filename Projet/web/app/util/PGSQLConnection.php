<?php namespace util;

use PDO;

/**
 * Handle the connection to the PostgreSQL database
 * @package util
 */
class PGSQLConnection
{
    private static $handle = null;

    public static function instance()
    {
        if (null === static::$handle) {
            $host = 'db';
            $port = '5432';
            $user = 'bdr';
            $password = 'bdr';
            $dbname = 'ctf';
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
            try {
                static::$handle = new PDO($dsn, $user, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                static::$handle->exec("SET search_path TO ctf");
            } catch (PDOException $e) {
                die("Database connection failure: " . $e->getMessage());
            }
        }
        return static::$handle;
    }

}



