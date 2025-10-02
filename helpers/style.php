<?php

if(!function_exists('style')){
    function style($name){
        $name = is_array($name) ? $name : func_get_args();
        return \Fmk\Components\StylesComponent::addScript($name);
    }
}