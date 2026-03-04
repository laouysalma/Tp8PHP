<?php
namespace App\Security;

use App\Core\Request;
use App\Security\Auth;
use App\Security\Csrf;

class Middleware
{
    private $auth;
    private $csrf;

    public function __construct(Auth $auth, Csrf $csrf)
    {
        $this->auth = $auth;
        $this->csrf = $csrf;
    }

    public function requireAdmin(callable $handler): callable
    {
        return function (Request $request, array $params = []) use ($handler) {
            if (!$this->auth->isAuthenticated()) {
                header('Location: /login');
                http_response_code(302);
                exit; // important pour stopper l’exécution
            }
            return call_user_func($handler, $request, $params);
        };
    }

    public function requireCsrfPost(callable $handler): callable
    {
        return function (Request $request, array $params = []) use ($handler) {
            if ($request->getMethod() !== 'POST') { 
                return call_user_func($handler, $request, $params); 
            }

            $token = $request->getBodyParam('_csrf');
            if (!$this->csrf->verify($token)) {
                http_response_code(403);
                echo '403 CSRF invalid';
                exit;
            }

            return call_user_func($handler, $request, $params);
        };
    }
}