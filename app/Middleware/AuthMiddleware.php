<?php

namespace App\Middleware;

use App\Services\SessionManager;
use App\my\path\Request; // Replace it with your actual Reuest class
use App\my\path\Response; // Replace it with your actual Response class

class AuthMiddleware implements MiddlewareInterface
{

    public function __construct(private SessionManager $sessionManager)
    {
        //
    }

    public function handle(Request $request, Response $response, callable $next)
    {
        // Get session ID from cookie
        $sessionId = $request->cookie['session_id'] ?? null;
        
        // Check if session exists and user is authenticated
        if (!$sessionId || !$this->sessionManager->exists($sessionId)) {
            return $this->redirectToLogin($response);
        }

        // Check if user_id exists in session
        $userId = $this->sessionManager->get($sessionId, 'user_id');
        if (!$userId) {
            return $this->redirectToLogin($response);
        }

        // User is authenticated, continue to next middleware/controller
        return $next($request, $response);
    }

    private function redirectToLogin(Response $response): void
    {
        $response->status(302);
        $response->header('Location', '/login');
        exit;
    }
}
