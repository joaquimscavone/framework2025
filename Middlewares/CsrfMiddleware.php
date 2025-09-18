<?php

namespace Fmk\Middlewares;

use Fmk\Enums\Methods;
use Fmk\Facades\CSRF;
use Fmk\Facades\Request;
use Fmk\Facades\Router;
use Fmk\Interfaces\Middleware;

class CsrfMiddleware implements Middleware{
    public function check():bool{
        $request = Request::getInstance();
        if($request->getMethod() == Methods::POST->value && !CSRF::check($request->{CSRF::TOKEN_NAME})) {
            return false;
        }
        return true;
    }

    public function handle(){
        return Router::error403('CSRF Token Inválido');
    }
}