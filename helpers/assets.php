<?php

if(!function_exists('assets')){ 
    function assets($path){
        $url = '/assets/';
        if(defined('APPLICATION_URL')){
            $url = rtrim(constant('APPLICATION_URL'),'/') . '/assets/';
        }
        return "$url" . ltrim($path,'/');
    }
}   