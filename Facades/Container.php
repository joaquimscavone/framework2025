<?php

namespace App\Containers;

use Exception;
use Fmk\Facades\Controller;
use Fmk\Traits\Singleton;
use ReflectionClass;
use ReflectionMethod;

class Container {
    protected object $object;
    protected $method_name;
    protected $callback;
    // protected $instances = [];

    public function __construct(string|object $class_name, string $method_name, ?array $params = NULL) {
        $object = $this->setObject($class_name);
        if($object && $object instanceof Controller) {
            $this->setCallback($object, $method_name, $params);
        } else {
            throw new Exception("$class_name não é uma classe.");
        }
    }

    /**
     * Monta um callback com base no objeto, método e parâmetros.
     * @param mixed $object
     * @param mixed $method_name
     * @param mixed $params
     * @return array[]|bool
     */
    protected function setCallback($object, $method_name, $params = NULL) {
        $clone = new ReflectionClass($object);
        $method = $clone->getMethod($method_name);
        $args = $this->getArgs($method, $params);
        $this->callback = [[$object, $method_name], $args];
        return $this->callback;
        // return call_user_func_array([$controller, $method_name], $args);
    }

    public function getCallback() {
        return $this->callback;
    }

    public function getObject() {
        return $this->object;
    }

    /**
     * Verifica se a classe existe e retorna um objeto.
     * @param mixed $class
     */
    protected function setObject($class) {
        return $this->object = (!is_object($class) && class_exists($class)) ? new $class : $class;
    }

    /**
     * Retorna os argumentos de uma determinada função
     * @param \ReflectionMethod $method
     * @return array
     */
    protected function getArgs(ReflectionMethod $method, ?array $argumentos = NULL) {
        $params = $method->getParameters();
        if($argumentos) {
            return $this->checkArgs($argumentos, $params);
        }
        return $argumentos;
    }

    protected function checkArgs($argumentos, $params) {
        $args = [];
        foreach($params as $param) {
            $name = $param->getName();
            $type = $param->getType();
            $type_name =(string) $type;
            
            if(!$type || $type->isBuiltin()) {
                if(array_key_exists($name, $argumentos)) {
                    $args[] = $argumentos[$name];
                } else {
                    throw new Exception("$name é um argumento fraco e sem ideal.");
                }
            } else {
                $args[] = $this->instance($type_name);
            }
        }
        return $args;
    }

    /**
     * Retorna um objeto da dependência passada como parâmetro
     * @param mixed $class
     */
    protected function instance($class) {
        // if(isset($this->instances[$class])) {   resulta no mesmo objeto sedo passado em mais de uma dependência
        //     return $this->instances[$class];
        // }
        $clone = new ReflectionClass($class);
        $constructor = $clone->getConstructor();
        if(!$constructor) {
            $object = new $class;
        } else {
            if(in_array(Singleton::class, $clone->getTraitNames())) {
                $object = $class::getInstance();
            } else {
                $args = $this->getArgs($constructor);
                $object = $clone->newInstanceArgs($args);
            }
        }
        return $object;
    }
}