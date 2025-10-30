<?php

if(!function_exists('route')){ 
    function route($route_name){
        return Fmk\Facades\Router::getRouteByName($route_name);
    }
}   