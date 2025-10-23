<?php

namespace Fmk\Facades\Database;

use PDO;

class Connection
{
    protected static $connections;

    private function __construct()
    {
        // Private constructor to prevent instantiation
    }

    public static function getConnection($dns, $username = null, $password = null, $options = null)
    {
        $key = $dns . $username;
        $env = defined('APPLICATION_ENV') ? constant('APPLICAION_ENV') : 'production';
        if (!isset(self::$connections[$key])) {
            $pdo = new PDO($dns, $username, $password, $options);
            $env === 'production' || $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$connections[$key] = $pdo;
        }
        return self::$connections[$key];

    }

}