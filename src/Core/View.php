<?php
namespace App\Core;

class View
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        // base path vers le dossier views
        $this->basePath = rtrim($basePath, '/');
    }

    public function render(string $view, array $params = [], ?string $layout = 'layout.php'): void
    {
        // calcul du fichier vue
        $viewFile = $this->basePath . '/' . ltrim($view, '/');

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo 'Vue introuvable: ' . htmlspecialchars($viewFile, ENT_QUOTES, 'UTF-8');
            return;
        }

        // extraire les variables pour la vue
        extract($params, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // si layout existe, on l'inclut
        if ($layout) {
            $layoutFile = $this->basePath . '/' . ltrim($layout, '/');
            if (file_exists($layoutFile)) {
                require $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }
}