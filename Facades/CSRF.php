<?php

namespace Fmk\Facades;

use Fmk\Traits\Singleton;

class CSRF
{
    use Singleton;

    const TOKEN_NAME = '__csrf_token__';
    protected $hash;
    protected function __construct(){
        $session = Session::getInstance();
        if(!isset($session->{self::TOKEN_NAME})){
            $session->{self::TOKEN_NAME} = base64_encode(random_bytes(32));
        }
        $this->hash = $session->{self::TOKEN_NAME};
    }

    protected function token(){
        return $this->hash;
    }

    protected function check($hash){
        return $this->hash === $hash;
    }
}