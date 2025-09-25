<?php

namespace Fmk;

class Initialize
{
    public static function run()
    {
        self::loadConfigs();
    }

    private static function loadConfigs(){
        $configs_path = __DIR__.DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
        $constantes = require $configs_path.'constants.php';
        foreach($constantes as $key => $constante){
            $exp = '(\$[a-zA-Z0-9_]{1,})';
            if(preg_match_all($exp, $constante, $match)){
                foreach($match[0] as $combinacao){
                    $preconst = constant(str_replace('$','',$combinacao));
                    $constante = str_replace($combinacao,$preconst, $constante);
                }
            }
            define($key, $constante);
        }
    }
}