<?php
namespace App\Dao;

class Logger
{
    private $file;
    public function __construct(string $file)
    {
        $this->file = $file;
     // Créer le dossier s'il n'existe pas
        $dir = dirname($file);
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
    }
   // Méthode pour écrire INFO 
    public function info(string $message): void { $this->write('INFO', $message); }
   // Méthode pour écrire   ERROR
    public function error(string $message): void { $this->write('ERROR', $message); }
    // ecrire  dans le fichier log
    private function write(string $level, string $message): void
    {
        $line = sprintf("%s [%s] %s\n", date('Y-m-d H:i:s'), $level, $message);
        @file_put_contents($this->file, $line, FILE_APPEND);
    }
}