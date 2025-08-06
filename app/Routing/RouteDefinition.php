<?php

namespace App\Routing;

use App\Middleware\MiddlewareInterface;

class RouteDefinition
{
    private array $middlewares = [];

    public function __construct(
        private string $method,
        private string $path,
        private array $handler, // [ControllerClass::class, 'method']
    ) {}

    /**
     * Add middleware to this route
     */
    public function middleware(MiddlewareInterface $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    /**
     * Add multiple middlewares to this route
     */
    public function middlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middleware($middleware);
        }
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler(): array
    {
        return $this->handler;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
