<?php

namespace Fmk\Traits;

use Exception;
use Fmk\Interfaces\Middleware;

trait Middlewares
{
    protected array $middlewares = [
        \Fmk\Middlewares\CsrfMiddleware::class,
        \Fmk\Middlewares\DuplicateRequestGuardMiddleware::class
    ];

    public function middleware($middleware)
    {
        $middleware = is_array($middleware) ? $middleware : func_get_args();
        $this->middlewares = array_merge($this->middlewares, $middleware);
        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    public function swapMiddlewares()
    {
        return $this->middlewares;
    }

    public function checkMiddlewares(): bool
    {
        return $this->execMiddlewares(function () {
            return false;
        });
    }

    public function execMiddlewares(callable $handle = null)
    {
        foreach ($this->swapMiddlewares() as $classMiddleware) {
           
            $middleware = new $classMiddleware;
            if (!$middleware instanceof Middleware) {
                throw new Exception("$classMiddleware não implementa a interface do Middleware");
            }
            if (!$middleware->check()) {
                if (is_null($handle)) {
                    return $middleware->handle();
                }
                return $handle();
            }
        }
        return true;
    }
}