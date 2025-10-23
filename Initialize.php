<?php

namespace Fmk;

class Initialize
{
    public static function run()
    {
        self::loadConfigs();
        self::loadHelpers();
    }

    private static function loadConfigs()
    {
        $configs_path = __DIR__ . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR;
        $constantes = require $configs_path . 'constants.php';
        self::createConstants($constantes);
        defined('DATABASE_DRIVERS') || define('DATABASE_DRIVERS',require $configs_path.'database_drivers.php');
    }

    public static function createConstants($constants)
    {
        foreach ($constants as $key => $constante) {
            $exp = '(\$[a-zA-Z0-9_]{1,})';
            if (preg_match_all($exp, $constante, $match)) {
                foreach ($match[0] as $combinacao) {
                    $preconst = constant(str_replace('$', '', $combinacao));
                    $constante = str_replace($combinacao, $preconst, $constante);
                }
            }
            defined($key) || define($key, $constante);
        }
    }

    public static function loadHelpers()
    {
        $helpers_path = __DIR__ . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR;
        $files = glob($helpers_path . '*.php');
        foreach ($files as $file) {
            require_once $file;
        }
    }
}