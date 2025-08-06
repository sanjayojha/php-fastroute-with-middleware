<?php

namespace App\Middleware;

use App\my\path\Request; // Replace it with your actual Reuest class
use App\my\path\Response; // Replace it with your actual Response class

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
