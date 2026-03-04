<?php
namespace App\Core;

// Classe qui gère les routes (GET et POST)
class Router
{
    private $routes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get(string $pattern, callable $handler): void
    { $this->add('GET', $pattern, $handler); }

    public function post(string $pattern, callable $handler): void
    { $this->add('POST', $pattern, $handler); }

    private function add(string $method, string $pattern, callable $handler): void
    {
        $regex = $this->compile($pattern);
        $this->routes[$method][] = [
            'pattern' => $pattern,
            'regex' => $regex,
            'handler' => $handler
        ];
    }

    // Transformer les paramètres {id} en expression régulière
    private function compile(string $pattern): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . rtrim($regex, '/') . '$#';
    }

    // Chercher la route correspondante et exécuter le handler
    public function dispatch(Request $request): void
    {
        $method = $request->getMethod();
        $path = rtrim($request->getPath(), '/');
        if ($path === '') { $path = '/'; }

        foreach ($this->routes[$method] as $route) {
            if (preg_match($route['regex'], $path, $matches)) {
                $params = [];
                foreach ($matches as $k => $v) {
                    if (!is_int($k)) { $params[$k] = $v; }
                }
                call_user_func($route['handler'], $request, $params);
                return;
            }
        }

        http_response_code(404);
        header('Content-Type: text/plain; charset=utf-8');
        echo '404 Not Found: ' . $path;
    }
}