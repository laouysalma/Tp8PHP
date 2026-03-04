<?php
declare(strict_types=1);

use App\Container\AppFactory;
use App\Core\Request;

// Démarrer la session si pas déjà fait
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader simple
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = dirname(__DIR__) . '/src/';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, $len));
    $file = $baseDir . $relative . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Redirection uniquement pour la racine exacte
if ($_SERVER['REQUEST_URI'] === '/' || $_SERVER['REQUEST_URI'] === '/index.php') {
    header('Location: /etudiants');
    exit;
}

try {
    // Création de l'application
    $factory = new AppFactory();
    [$router, $request] = $factory->create();
    
    // Exécution du routeur
    $router->dispatch($request);
    
} catch (Exception $e) {
    // Gestion des erreurs
    http_response_code(500);
    echo "<h1>Erreur</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    // En mode développement, afficher la stack trace
    if (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === 'dev') {
        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    }
}