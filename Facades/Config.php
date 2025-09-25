<?php

namespace Fmk\Facades;

use Exception;

class Config
{
    private static array $configs = [];

    /**
     * Retorna um valor de um arquivo de configuração
     * arquivo.posicao.posicao
     * @param string $key
     * @param mixed $default
     */
    public static function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        //['middlewares'];
        $file_name = array_shift($keys); //middlewares;
        //$keys = [];
        $data = self::getFile($file_name);
        return self::filter($data, $key, $default);
    }

    private static function getFile($file_name)
    {
        $config = &self::$configs;
        if (!array_key_exists($file_name, $config)) {
            $config_path = defined('APPLICATION_PATH') ? constant('APPLICATION_PATH') : '';
            $config_path = defined('CONFIG_PATH') ? constant('CONFIG_PATH') : $config_path;
            $file = $config_path . DIRECTORY_SEPARATOR . $file_name . ".php";
            if (!file_exists($file)) {
                throw new Exception("O arquivo $file não foi encontrado");
            }
            $config[$file_name] = require $file;
        }
        return $config[$file_name];

    }

    private static function filter($data, $filter, $default)
    {
        foreach ($filter as $key) {
            if (!isset($data[$key])) {
                return $default;
            }
            $data = $data[$key];
        }
        return $data;
    }
}