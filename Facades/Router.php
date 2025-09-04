<?php

namespace Fmk\Facades;

use Exception;
use Fmk\Enums\Methods;
use Fmk\Traits\Singleton;

class Router
{
    protected array $routes;
    use Singleton;
   protected static $private_methods = ['add'];

    protected function swapName($old_name, $new_name){
        if(array_key_exists($new_name, $this->routes)){
            throw new Exception("Já existe uma rota com o nome $new_name");
        }
        if(!array_key_exists($old_name, $this->routes)){
            throw new Exception("A rota $old_name não existe");
        }

        $this->routes[$new_name] = $this->routes[$old_name];
        unset($this->routes[$old_name]);

    }
    protected function add($uri, Methods $method, $callback){
        $name = count($this->routes);
        $this->routes[$name] = new Route($name,$uri,$method,$callback);
        return $this->routes[$name];
    }

    protected function get($uri, $callback){
        return $this->add($uri, Methods::GET, $callback);
    }

    protected function post($uri, $callback){
          return $this->add($uri, Methods::POST, $callback);
    }


} 