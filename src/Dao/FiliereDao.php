<?php

namespace App\Dao;

use PDO;
use App\Security\Sanitizer;
use App\Security\Validator;

class FiliereDao
{
private $pdo;
private $logger;

public function __construct(PDO $pdo, Logger $logger)
{
$this->pdo = $pdo;
$this->logger = $logger;
}

// Recuperer toutes les filieres
public function findAll(): array
{
$stmt = $this->pdo->query('SELECT id, code, libelle FROM filiere ORDER BY libelle');
return $stmt->fetchAll();
}

// Recuperer une filiere par son id
public function findById(int $id): ?array
{
$stmt = $this->pdo->prepare('SELECT id, code, libelle FROM filiere WHERE id = ?');
$stmt->execute([$id]);
$row = $stmt->fetch();
return $row ?: null;
}


// Affiche la liste des etudiants avec recherche et pagination
public function index(\App\Core\Request $request): void
{
// Recuperation et sanitation des parametres GET
$q = Sanitizer::string($request->getQueryParam('q', ''), 100);
$filiereId = (int) $request->getQueryParam('filiere_id', 0);
$page = (int) $request->getQueryParam('page', 1);
$size = (int) $request->getQueryParam('size', 5);

// Recuperation des etudiants selon criteres
$total = $this->etudiantDao->countSearch($q, $filiereId ?: null);
$items = $this->etudiantDao->searchPaginated($q, $filiereId ?: null, $page, $size);
$totalPages = max(1, (int) ceil($total / max(1, $size)));

$filieres = $this->filiereDao->findAll();

// Affichage de la vue avec tous les parametres
$this->render('etudiant/index.php', [
'etudiants' => $items,
 'q' => $q,
 'filiereId' => $filiereId,
 'filieres' => $filieres,
 'page' => max(1, $page),
 'size' => max(1, $size),
 'total' => $total,
 'totalPages' => $totalPages,
]);
}

// Nettoie les donnees saisies avant insertion ou update
private function sanitize(array $data): array
{
$data = Sanitizer::trimArray($data);
return [
'cne' => strtoupper(Sanitizer::string($data['cne'] ?? '', 20)),
 'nom' => Sanitizer::string($data['nom'] ?? '', 50),
 'prenom' => Sanitizer::string($data['prenom'] ?? '', 50),
 'email' => Sanitizer::email($data['email'] ?? ''),
 'filiere_id' => (int) ($data['filiere_id'] ?? 0),
];
}

// Valide les donnees saisies et retourne un tableau d'erreurs
private function validate(array $data, ?int $id = null): array
{
$errors = [];

// Verification CNE, nom, prenom, email et filiere
if (!Validator::cne($data['cne'])) { $errors['cne'] = 'CNE requis (A-Z, 0-9, 6-20).';
}
if ($data['nom'] === '' ||!Validator::maxLen($data['nom'], 50)) { $errors['nom'] = 'Nom requis (<=50).';
}
if ($data['prenom'] === '' ||!Validator::maxLen($data['prenom'], 50)) { $errors['prenom'] = 'Prenom requis (<=50).';
}
if (!Validator::email($data['email']) ||!Validator::maxLen($data['email'], 100)) { $errors['email'] = 'Email invalide (<=100).';
}
if ($data['filiere_id'] <= 0 ||!$this->filiereDao->findById((int) $data['filiere_id'])) { $errors['filiere_id'] = 'Filiere invalide.';
}

return $errors;
}
}