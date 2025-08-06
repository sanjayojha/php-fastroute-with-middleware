<?php

declare(strict_types=1);
// Autoloader for our classes and vendor packages
require_once __DIR__ . '/../vendor/autoload.php';

use App\my\path\Request; // Replace it with your actual Reuest class
use App\my\path\Response; // Replace it with your actual Response class

$request = new Request();
$response = new Response();

// Get the router dispatcher instance from our routes file
$dispatcher = require __DIR__ . '/../app/routes.php';


try {
    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri());

    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            $response->status(404);
            $response->sendText('Not Found');
            break;

        case Dispatcher::METHOD_NOT_ALLOWED:
            $response->status(405);
            $response->sendText('Method Not Allowed');
            break;

        case Dispatcher::FOUND:
            $routeData = $routeInfo[1];
            $vars = $routeInfo[2];

            $handler = $routeData['handler'];
            $middlewares = $routeData['middlewares'] ?? [];

            [$controllerClass, $method] = $handler;

            // Create the final handler (controller action)
            $finalHandler = function (Request $req, Response $res) use ($controllerClass, $method, $vars) {
                $controllerInstance = new $controllerClass($req, $res);
                
                $content = $controllerInstance->$method($vars);

                $res->header('Content-Type', 'text/html');
                $res->sendHTML($content);
            };

            // Execute middleware pipeline
            if (!empty($middlewares)) {
                $pipeline = new MiddlewarePipeline();
                
                foreach ($middlewares as $middleware) {
                    $pipeline->add($middleware);
                }
                
                $pipeline->execute($request, $response, $finalHandler);
            } else {
                // No middleware, execute handler directly
                $finalHandler($request, $response);
            }
            
            break;
    }
} catch (\Throwable $e) {
    // Show a minimal message on the terminal
    echo "ERROR: {$e->getMessage()}\n";

    // Send a generic 500 error to the browser
    $response->status(500);
    $response->sendText('Internal Server Error');
}
