<?php

namespace App\Middleware;

use App\Services\SessionManager;
use App\my\path\Request; // Replace it with your actual Reuest class
use App\my\path\Response; // Replace it with your actual Response class

class RoleMiddleware implements MiddlewareInterface
{

    private array $allowedRoles;

    public function __construct(private SessionManager $sessionManager, array $allowedRoles = [])
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function handle(Request $request, Response $response, callable $next)
    {
        // Get session ID from cookie
        $sessionId = $request->cookie['session_id'] ?? null;
        
        if (!$sessionId || !$this->sessionManager->exists($sessionId)) {
            return $this->accessDenied($response, 'Authentication required');
        }

        // Get user role from session
        $userRole = $this->sessionManager->get($sessionId, 'role');
        
        if (!$userRole || !in_array($userRole, $this->allowedRoles)) {
            return $this->accessDenied($response, 'Insufficient permissions');
        }

        // User has required role, continue
        return $next($request, $response);
    }

    private function accessDenied(Response $response, string $message): void
    {
        $response->status(403);
        $response->header('Content-Type', 'text/html');
        $response->sendHTML("
            <html>
                <body>
                    <h1>Access Denied</h1>
                    <p>{$message}</p>
                    <a href='/'>Go Home</a>
                </body>
            </html>
        ");
    }
}
