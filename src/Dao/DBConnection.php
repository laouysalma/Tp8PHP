<?php
namespace App\Dao;

use PDO; use PDOException;

class DBConnection
{
    public static function create(string $host, string $db, string $user, string $pass, string $charset, Logger $logger): PDO
    {
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            return new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            $logger->error('DB Connection failed: ' . $e->getMessage());
            http_response_code(500);
            die('Database connection error');
        }
    }
}