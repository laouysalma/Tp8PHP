<?php

namespace App\Dao;

use PDO;

class EtudiantDao
{
private $pdo;
private $logger;

public function __construct(PDO $pdo, Logger $logger)
{
$this->pdo = $pdo;
$this->logger = $logger;
}

public function countAll(): int
{
$stmt = $this->pdo->query('SELECT COUNT(*) AS c FROM etudiant');
$row = $stmt->fetch();
return (int) $row['c'];
}

public function findAllPaginated(int $page, int $size): array
{
$page = max(1, $page);
$size = max(1, min(100, $size));
$offset = ($page - 1) * $size;

$sql = 'SELECT e.id, e.cne, e.nom, e.prenom, e.email, e.filiere_id, f.code AS filiere_code, f.libelle AS filiere_libelle
                FROM etudiant e JOIN filiere f ON e.filiere_id = f.id
                ORDER BY e.id DESC
                LIMIT :lim OFFSET :off';
$stmt = $this->pdo->prepare($sql);
$stmt->bindValue(':lim', $size, PDO::PARAM_INT);
$stmt->bindValue(':off', $offset, PDO::PARAM_INT);
$stmt->execute();

return $stmt->fetchAll();
}

// Trouver un etudiant par son id
public function findById(int $id): ?array
{
$stmt = $this->pdo->prepare(
'SELECT e.*, f.code AS filiere_code, f.libelle AS filiere_libelle 
             FROM etudiant e 
             JOIN filiere f ON e.filiere_id=f.id 
             WHERE e.id=?'
);
$stmt->execute([$id]);
$row = $stmt->fetch();
return $row ?: null;
}

// Creer un nouvel etudiant
public function create(array $data): int
{
$sql = 'INSERT INTO etudiant (cne, nom, prenom, email, filiere_id) VALUES (?,?,?,?,?)';
$stmt = $this->pdo->prepare($sql);
$stmt->execute([
$data['cne'], $data['nom'], $data['prenom'], $data['email'], (int) $data['filiere_id']
]);
return (int) $this->pdo->lastInsertId();
}

// Mettre a jour un etudiant existant
public function update(int $id, array $data): bool
{
$sql = 'UPDATE etudiant SET cne=?, nom=?, prenom=?, email=?, filiere_id=? WHERE id=?';
$stmt = $this->pdo->prepare($sql);
return $stmt->execute([
$data['cne'], $data['nom'], $data['prenom'], $data['email'], (int) $data['filiere_id'], $id
]);
}

// Supprimer un etudiant
public function delete(int $id): bool
{
$stmt = $this->pdo->prepare('DELETE FROM etudiant WHERE id=?');
return $stmt->execute([$id]);
}
// Compte le nombre d'etudiants correspondant aux criteres de recherche
public function countSearch(?string $q, ?int $filiereId): int
{
$where = [];
$params = [];

// Filtrage par mot-cle dans CNE, nom, prenom ou email
if ($q !== null && $q !== '') {
$where[] = '(e.cne LIKE ? OR e.nom LIKE ? OR e.prenom LIKE ? OR e.email LIKE ?)';
$like = '%' . $q . '%';
array_push($params, $like, $like, $like, $like);
}

// Filtrage par filiere si specifiee
if ($filiereId && $filiereId > 0) {
$where[] = 'e.filiere_id = ?';
$params[] = $filiereId;
}

$sql = 'SELECT COUNT(*) c FROM etudiant e'
. (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
$stmt = $this->pdo->prepare($sql);
$stmt->execute($params);
$row = $stmt->fetch();
return (int) $row['c'];
}

// Recherche paginee d'etudiants selon criteres et filiere
public function searchPaginated(?string $q, ?int $filiereId, int $page, int $size): array
{
$page = max(1, $page);
$size = max(1, min(100, $size));
$offset = ($page - 1) * $size;

$where = [];
$params = [];

// Filtrage par mot-cle dans CNE, nom, prenom ou email
if ($q !== null && $q !== '') {
$where[] = '(e.cne LIKE ? OR e.nom LIKE ? OR e.prenom LIKE ? OR e.email LIKE ?)';
$like = '%' . $q . '%';
array_push($params, $like, $like, $like, $like);
}

// Filtrage par filiere si specifiee
if ($filiereId && $filiereId > 0) {
$where[] = 'e.filiere_id = ?';
$params[] = $filiereId;
}

// Construction de la requete avec jointure et pagination
$sql = 'SELECT e.id, e.cne, e.nom, e.prenom, e.email, e.filiere_id, 
                   f.code AS filiere_code, f.libelle AS filiere_libelle
            FROM etudiant e 
            JOIN filiere f ON e.filiere_id = f.id'
. (count($where) ? ' WHERE ' . implode(' AND ', $where) : '') .
' ORDER BY e.id DESC LIMIT ? OFFSET ?';

$stmt = $this->pdo->prepare($sql);
foreach ($params as $i => $val) {
$stmt->bindValue($i+1, $val, is_int($val)?PDO::PARAM_INT:PDO::PARAM_STR);
}
$stmt->bindValue(count($params) + 1, $size, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();

// Retourne le tableau d'etudiants trouve
return $stmt->fetchAll();
}
}