<?php
namespace App\Container;

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Core\View;
use App\Controller\EtudiantController;
use App\Controller\AuthController;
use App\Dao\DBConnection;
use App\Dao\Logger;
use App\Dao\EtudiantDao;
use App\Dao\FiliereDao;
use App\Dao\AdminDao;
use App\Security\Auth;
use App\Security\Csrf;
use App\Security\Middleware;

class AppFactory
{
    public function create(): array
    {
        // Connexion DB
        $logger = new Logger(__DIR__ . '/../../logs/app.log');
        $pdo = DBConnection::create('127.0.0.1','gestion_etudiants_pdo','root','1234','utf8mb4',$logger);

        // Auth & CSRF
        $adminDao = new AdminDao($pdo, $logger);
        $auth = new Auth($adminDao);
        $auth->startSession();
        $csrf = new Csrf();
        $csrf->token();
        $mw = new Middleware($auth, $csrf);

        // MVC
        $view = new View(__DIR__ . '/../../views');
        $response = new Response();
        $request = new Request();
        $router = new Router();

        $etudiantDao = new EtudiantDao($pdo, $logger);
        $filiereDao = new FiliereDao($pdo, $logger);

        $etudiantController = new EtudiantController($view, $response, $etudiantDao, $filiereDao);
        $authController = new AuthController($view, $response, $auth, $csrf);

        // Routes publiques
        $router->get('/', function() use ($response, $auth) {
            if (!$auth->isAuthenticated()) {
                $response->redirect('/login');
            } else {
                $response->redirect('/etudiants');
            }
        });

        $router->get('/login', [$authController, 'loginForm']);
        $router->post('/login', $mw->requireCsrfPost([$authController, 'login']));
        $router->post('/logout', $mw->requireCsrfPost([$authController, 'logout']));

        // Routes protégées
        $router->get('/etudiants', $mw->requireAdmin([$etudiantController, 'index']));
        $router->get('/etudiants/{id}', $mw->requireAdmin([$etudiantController, 'show']));
        $router->get('/etudiants/create', $mw->requireAdmin([$etudiantController, 'create']));
        $router->post('/etudiants/store', $mw->requireAdmin($mw->requireCsrfPost([$etudiantController, 'store'])));
        $router->get('/etudiants/{id}/edit', $mw->requireAdmin([$etudiantController, 'edit']));
        $router->post('/etudiants/{id}/update', $mw->requireAdmin($mw->requireCsrfPost([$etudiantController, 'update'])));
        $router->post('/etudiants/{id}/delete', $mw->requireAdmin($mw->requireCsrfPost([$etudiantController, 'delete'])));

        return [$router, $request];
    }
}