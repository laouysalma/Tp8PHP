<?php
namespace App\Controller;

use App\Core\Request;
use App\Core\View;
use App\Core\Response;
use App\Security\Auth;
use App\Security\Csrf;

class AuthController extends BaseController
{
    private Auth $auth;

    // Initialise le controller avec Auth et CSRF
    public function __construct(
        View $view,
        Response $response,
        Auth $auth,
        Csrf $csrf
    ) {
        // On passe bien $csrf au parent
        parent::__construct($view, $response, $csrf);
        $this->auth = $auth;
    }

    // Affiche le formulaire de login
    public function loginForm(): void
    {
        $this->render('auth/login.php', [
            'errors' => [],
            'old' => []
        ]);
    }

    // Traite la connexion
    public function login(Request $request): void
    {
        $username = trim((string)$request->getBodyParam('username'));
        $password = (string)$request->getBodyParam('password');

        if ($username === '' || $password === '') {
            $this->render('auth/login', [
                'errors' => ['global' => 'Identifiants requis.'],
                'old' => ['username' => $username]
            ]);
            return;
        }

        if ($this->auth->login($username, $password)) {
            $this->redirect('/etudiants');
            return;
        }

        // Login invalide
        $this->render('auth/login', [
            'errors' => ['global' => 'Login ou mot de passe invalide.'],
            'old' => ['username' => $username]
        ]);
    }

    // Déconnexion
    public function logout(): void
    {
        $this->auth->logout();
        $this->redirect('/login');
    }
}