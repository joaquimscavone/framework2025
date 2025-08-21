<?php

namespace Fmk\Facades;

class Request
{
    protected static $instance;
    protected $uri;
    protected $method;
    protected $data;
    const REQUEST_KEY = 'request_uri';
    final private function __construct(){
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if($this->method === 'GET'){
            $this->data =  $_GET ?? [];
        }else{
            $this->data =  $_POST ?? [];
        }
        $this->uri = $this->data[static::REQUEST_KEY] ?? '';
        unset($this->data[static::REQUEST_KEY]);
    }

    public static function getInstance(){
        if(is_null(static::$instance)){
            static::$instance = new static;
        }
        return static::$instance;
    }

    public function __get($name){
        return $this->data[$name] ?? null;
    }
    public function __isset($name){
        return isset($this->data[$name]);
    }

    public function all(){
        return $this->data;
    }

    public function getUri(){
        return $this->uri;
    }
    public function getMethod(){
        return $this->method;
    }

    

}
