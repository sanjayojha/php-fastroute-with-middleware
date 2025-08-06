<?php

use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\AdminController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Routing\RouteDefinition;
use App\Services\SessionManager;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// Define routes with their middleware requirements
$routes = [
    // Public routes (no middleware)
    new RouteDefinition('GET', '/', [HomeController::class, 'index']),
    new RouteDefinition('GET', '/login', [UserController::class, 'loginForm']),
    new RouteDefinition('POST', '/login', [UserController::class, 'handleLogin']),
    
    // Routes requiring authentication
    (new RouteDefinition('GET', '/dashboard', [DashboardController::class, 'index']))
        ->middleware(new AuthMiddleware(new SessionManager)),
    
    (new RouteDefinition('GET', '/profile', [UserController::class, 'profile']))
        ->middleware(new AuthMiddleware(new SessionManager)),
    
    (new RouteDefinition('GET', '/logout', [UserController::class, 'logout']))
        ->middleware(new AuthMiddleware()),
    
    // Admin-only routes
    (new RouteDefinition('GET', '/admin', [AdminController::class, 'index']))
        ->middlewares([
            new AuthMiddleware(new SessionManager),
            new RoleMiddleware(new SessionManager, ['admin'])
        ]),
    
    (new RouteDefinition('GET', '/admin/users', [AdminController::class, 'users']))
        ->middlewares([
            new AuthMiddleware(new SessionManager),
            new RoleMiddleware(new SessionManager, ['admin'])
        ]),

    // Routes for multiple roles
    (new RouteDefinition('GET', '/moderator', [DashboardController::class, 'moderator']))
        ->middlewares([
            new AuthMiddleware(),
            new RoleMiddleware(new SessionManager, ['admin', 'moderator'])
        ]),
];

// Create the FastRoute dispatcher
return simpleDispatcher(function (RouteCollector $r) use ($routes) {
    foreach ($routes as $route) {
        // Store both handler and middlewares in the route info
        $r->addRoute(
            $route->getMethod(), 
            $route->getPath(), 
            [
                'handler' => $route->getHandler(),
                'middlewares' => $route->getMiddlewares()
            ]
        );
    }
});
