<?php

if(!function_exists('script')){
    function script($name){
        $name = is_array($name) ? $name : func_get_args();
        return \Fmk\Components\ScriptsComponent::addScript($name);
    }
}