<?php
namespace App\Controller;

use App\Core\Response;
use App\Core\View;
use App\Security\Csrf;

class BaseController
{
    protected $view; 
    protected $response; 
    protected $csrf;

    // Initialise le controller avec la vue, la reponse et CSRF
    public function __construct(View $view, Response $response, ?Csrf $csrf = null)
    { 
        $this->view = $view; 
        $this->response = $response; 
        $this->csrf = $csrf; 
    }

    // Affiche une vue avec des parametres
    protected function render(string $view, array $params = []): void
    { 
        $this->view->render($view, $params); 
    }

    // Redirige vers une URL avec code HTTP
    protected function redirect(string $url, int $status = 302): void
    { 
        $this->response->redirect($url, $status); 
    }

    protected function json($data, int $status = 200): void
    { 
        $this->response->json($data, $status); 
    }

    // Echappe une chaine pour eviter XSS
    protected function e(?string $s): string
    { 
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); 
    }

    // Genere le champ cache CSRF pour les formulaires
    protected function csrfField(): string
    {
        $token = $_SESSION['csrf_token'] ?? '';
        return '<input type="hidden" name="_csrf" value="' . $this->e($token) . '">';
    }
}