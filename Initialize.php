<?php

namespace Fmk;

class Initialize
{
    public static function run()
    {
        // Initialization code here
    }

    private static function loadConfigs(){
        $configs_path = __DIR__.DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR;
        $constantes = require $configs_path.'constantes.php';
        foreach($constantes as $key => $constante){
            if(!defined($key))
            {
                
            }
        }
    }
}