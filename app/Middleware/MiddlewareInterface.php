<?php

namespace App\Middleware;

use Swoole\Http\Request;
use Swoole\Http\Response;

interface MiddlewareInterface
{
    /**
     * Handle the middleware logic
     * 
     * @param Request $request
     * @param Response $response
     * @param callable $next - The next middleware or controller action
     * @return mixed
     */
    public function handle(Request $request, Response $response, callable $next);
}
