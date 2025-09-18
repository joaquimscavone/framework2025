<?php

namespace Fmk\Interfaces;

interface Middleware{
    //handle será executado quando o check retornar false e deve conter
    //  a lógica de redirecionamento ou resposta apropriada
    public function handle();
    //check deve conter a lógica de verificação da condição
    public function check():bool;
}   