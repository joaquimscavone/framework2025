<?php

namespace Fmk\Middlewares;

use Fmk\Enums\Methods;
use Fmk\Facades\Request;
use Fmk\Facades\Router;
use Fmk\Facades\Session;
use Fmk\Interfaces\Middleware;

class DuplicateRequestGuardMiddleware implements Middleware{
    public function check():bool{
        $request = Request::getInstance();
        if($request->getMethod() == Methods::POST->value){
            $request_old = Session::request();
            return $request->getMethod() != $request_old['method']
              || $request->getUri() != $request_old['uri']
              || md5(implode('',$request->all())) != md5(implode('',$request_old['data']));
        }
        return true;
    }

    public function handle(){
        return Request::back();
    }
}