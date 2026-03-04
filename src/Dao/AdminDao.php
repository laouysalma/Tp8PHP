<?php
namespace App\Dao;

use PDO;

class AdminDao
{
    private $pdo; 
    private $logger;

    // Initialise le DAO avec la connexion PDO et le logger
    public function __construct(PDO $pdo, Logger $logger)
    { 
        $this->pdo = $pdo; 
        $this->logger = $logger; 
    }

    // Cherche un admin dans la base via son nom d'utilisateur
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id, username, password_hash FROM admin WHERE username = ?');
        $stmt->execute([$username]);
        
        // Retourne la ligne si trouvee, sinon null
        $row = $stmt->fetch();
        return $row ?: null;
    }
}