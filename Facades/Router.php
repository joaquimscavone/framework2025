<?php

namespace Fmk\Facades;

use Exception;
use Fmk\Enums\Methods;
use Fmk\Traits\Singleton;

class Router
{
    use Singleton;
    protected array $routes = [];

    protected static $error404;
    protected static $error403;

    // Override the Singleton constructor to configure trait-level private methods
    protected function __construct()
    {
        static::$private_methods = ['add'];
    }

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

    protected function getRouteByName($name){
        return $this->routes[$name] ?? null;
    }

    protected function checkUri($uri){
        if(empty(trim($uri)) || $uri =='/'){
            return "/";
        }
        $uri = (substr($uri, 0, 1)==="/")?$uri:"/$uri";
        return rtrim($uri,"/");
    }

    protected function getRouteByUri($uri, $method= Methods::GET){
        /**
         * /quartos/35/pedidos/15
         * /quartos/{quarto}/pedidos/{pedido}
         * /quartos/([a-zA-Z0-9_\-|\s]{1,})/pedidos/([a-zA-Z0-9_\-|\s]{1,})
         */
        $uri = $this->checkUri($uri);
        foreach($this->routes as $key => $route){
            if($route->getMethod()->value != $method){
                continue;
            }
            $expression = preg_replace("(\{[a-z0-9_]{1,}\})","([a-zA-Z0-9_\-|\s]{1,})", $route->getUri());
            if(preg_match("#^($expression)$#i",$uri, $matches) === 1){
                array_shift($matches);
                array_shift($matches);
                $route->defineParamns($matches);
                return $route;
            }
        }

        return false;
    }

    public static function defineError404(callable $callback){
        static::$error404 = $callback;
    }

    public static function defineError403(callable $callback){
        static::$error403 = $callback;
    }

    public static function error404(string $msg = "Not Found!"){
        if(is_callable(static::$error404)){
            call_user_func(static::$error404, ['msg' => $msg]);
        }else{
            http_response_code(404);
            echo "Erro 404: $msg";
        }
    }
    public static function error403(string $msg = "Forbidden!"){
        if(is_callable(static::$error403)){
            call_user_func(static::$error403, ['msg' => $msg]);
        }else{
            http_response_code(403);
            echo "Erro 403: $msg";
        }
    }

} 