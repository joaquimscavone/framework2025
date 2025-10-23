<?php

namespace Fmk\Facades\Database\Drivers;

use PDO;
class Mysql extends Driver
{

    protected array $parameters_default = [
        'port' => 3306,
        'host' => 'localhost',
        'options' => [PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8']
    ];
    protected function getDns():string
    {
        return "mysql:host=$this->host;dbname=$this->database";
    }
}