<?php

namespace App\Middleware;

use App\my\path\Request; // Replace it with your actual Reuest class
use App\my\path\Response; // Replace it with your actual Response class

class MiddlewarePipeline
{
    private array $middlewares = [];

    /**
     * Add middleware to the pipeline
     */
    public function add(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Execute the middleware pipeline
     */
    public function execute(Request $request, Response $response, callable $finalHandler)
    {
        return $this->createPipeline($this->middlewares)($request, $response, $finalHandler);
    }

    /**
     * Create the middleware pipeline with proper nesting
     */
    private function createPipeline(array $middlewares): callable
    {
        return function (Request $request, Response $response, callable $finalHandler) use ($middlewares) {
            if (empty($middlewares)) {
                return $finalHandler($request, $response);
            }

            $middleware = array_shift($middlewares);
            $next = $this->createPipeline($middlewares);

            return $middleware->handle($request, $response, function ($req, $res) use ($next, $finalHandler) {
                return $next($req, $res, $finalHandler);
            });
        };
    }
}
