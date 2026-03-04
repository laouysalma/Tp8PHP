<?php
namespace App\Security;

use App\Dao\AdminDao;

class Auth
{
    private $adminDao;
    private $sessionKeyUser = 'admin_id';
    private $sessionKeyLast = 'last_activity';
    private $timeoutSeconds = 20 * 60; // 20 minutes

    public function __construct(AdminDao $adminDao)
    { 
        $this->adminDao = $adminDao; 
    }

    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) { return; }

        // Demarre une session securisee avec parametres adaptes  
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
        $this->enforceTimeout();
    }

    private function enforceTimeout(): void
    {
        $now = time();

        // Verifie l’inactivite de l’utilisateur et applique une deconnexion automatique.
        if (!isset($_SESSION[$this->sessionKeyLast])) {
            $_SESSION[$this->sessionKeyLast] = $now;
            return;
        }

        if (($now - (int)$_SESSION[$this->sessionKeyLast]) > $this->timeoutSeconds) {
            $this->logout();
            return;
        }

        $_SESSION[$this->sessionKeyLast] = $now;
    }

    public function login(string $username, string $password): bool
    {
        $user = $this->adminDao->findByUsername($username);
        if (!$user) { return false; }
        if (!password_verify($password, $user['password_hash'])) { return false; }

        session_regenerate_id(true);

        // Authentifie l’administrateur et stocke son identifiant en session.
        $_SESSION[$this->sessionKeyUser] = (int)$user['id'];
        $_SESSION[$this->sessionKeyLast] = time();

        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), 
                '', 
                time() - 42000, 
                $params['path'], 
                $params['domain'], 
                $params['secure'], 
                $params['httponly']
            );
        }
        @session_destroy();
    }

    public function isAuthenticated(): bool
    { 
        return isset($_SESSION[$this->sessionKeyUser]) && (int)$_SESSION[$this->sessionKeyUser] > 0; 
    }

    public function getAdminId(): ?int
    { 
        return $this->isAuthenticated() ? (int)$_SESSION[$this->sessionKeyUser] : null; 
    }
}