<?php

namespace Fmk\Traits;

use Exception;

trait Singleton
{
    protected static $instance;

    protected static $private_methods = [];

    protected function __construct(){}
    protected function __clone(){}
    public function __wakeup(){}

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public static function __callStatic($name, $arguments = [])
    {
        if(in_array($name, static::$private_methods)){
            throw new Exception("Método $name é privado");
        }
        return call_user_func_array([static::getInstance(), $name], $arguments);

    }

    public function __call($name, $arguments = [])
    {
         if(in_array($name, static::$private_methods)){
            throw new Exception("Método $name é privado");
        }
        if(method_exists($this,$name)){
            return call_user_func_array([static::getInstance(), $name], $arguments);
        }
        throw new Exception("Método $name não existe");
    }
}