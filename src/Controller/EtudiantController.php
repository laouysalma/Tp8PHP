<?php

namespace App\Controller;

use App\Core\Request;
use App\Dao\EtudiantDao;
use App\Dao\FiliereDao;

class EtudiantController extends BaseController
{
private $etudiantDao;
private $filiereDao;

public function __construct($view, $response, EtudiantDao $etudiantDao, FiliereDao $filiereDao)
{
parent::__construct($view, $response);
$this->etudiantDao = $etudiantDao;
$this->filiereDao = $filiereDao;
}

public function index(Request $request): void
{
$page = (int) ($request->getQueryParam('page', 1));
$size = (int) ($request->getQueryParam('size', 5));

// Récupérer total et liste paginée
$total = $this->etudiantDao->countAll();
$students = $this->etudiantDao->findAllPaginated($page, $size);
$totalPages = max(1, (int) ceil($total / max(1, $size)));

// Affichage de la vue avec paramètres
$this->render('etudiant/index.php', [
'etudiants' => $students,
 'page' => max(1, $page),
 'size' => max(1, $size),
 'total' => $total,
 'totalPages' => $totalPages,
]);
}

public function show(Request $request, array $params): void
{
$id = (int) ($params['id'] ?? 0);
$e = $this->etudiantDao->findById($id);
if (!$e) { http_response_code(404);
echo 'Attention etudiant introuvable';
return;
}
$this->render('etudiant/show.php', ['e' => $e]);
}

public function create(): void
{
$filieres = $this->filiereDao->findAll();
$this->render('etudiant/create.php', ['filieres' => $filieres, 'errors' => []]);
}

public function store(Request $request): void
{
$data = $this->sanitize($request->allBody());
$errors = $this->validate($data);
if ($errors) {
$filieres = $this->filiereDao->findAll();
$this->render('etudiant/create.php', ['filieres' => $filieres, 'errors' => $errors, 'old' => $data]);
return;
}
$id = $this->etudiantDao->create($data);
$this->redirect('/etudiants/' . $id);
}

public function edit(Request $request, array $params): void
{
$id = (int) ($params['id'] ?? 0);
$e = $this->etudiantDao->findById($id);
if (!$e) { http_response_code(404);
echo 'Attention etudiant introuvable';
return;
}
$filieres = $this->filiereDao->findAll();
$this->render('etudiant/edit.php', ['e' => $e, 'filieres' => $filieres, 'errors' => []]);
}

public function update(Request $request, array $params): void
{
$id = (int) ($params['id'] ?? 0);
$e = $this->etudiantDao->findById($id);
if (!$e) { http_response_code(404);
echo 'Étudiant introuvable';
return;
}

$data = $this->sanitize($request->allBody());
$errors = $this->validate($data, $id);
if ($errors) {
$filieres = $this->filiereDao->findAll();
$this->render('etudiant/edit.php', ['e' => array_merge($e, $data), 'filieres' => $filieres, 'errors' => $errors]);
return;
}
$this->etudiantDao->update($id, $data);
$this->redirect('/etudiants/' . $id);
}

public function delete(Request $request, array $params): void
{
$id = (int) ($params['id'] ?? 0);
if ($id > 0) { $this->etudiantDao->delete($id);
}
$this->redirect('/etudiants');
}

// Optionnel API JSON
public function apiList(Request $request): void
{
$page = (int) ($request->getQueryParam('page', 1));
$size = (int) ($request->getQueryParam('size', 5));
$total = $this->etudiantDao->countAll();
$items = $this->etudiantDao->findAllPaginated($page, $size);
$this->json([
'page' => $page,
 'size' => $size,
 'total' => $total,
 'totalPages' => (int) ceil($total / max(1, $size)),
 'items' => $items,
]);
}

private function sanitize(array $data): array
{
return [
'cne' => trim((string) ($data['cne'] ?? '')),
 'nom' => trim((string) ($data['nom'] ?? '')),
 'prenom' => trim((string) ($data['prenom'] ?? '')),
 'email' => trim((string) ($data['email'] ?? '')),
 'filiere_id' => (int) ($data['filiere_id'] ?? 0),
];
}

private function validate(array $data, ?int $id = null): array
{
$errors = [];
if ($data['cne'] === '' ||!preg_match('/^[A-Z0-9]{6,20}$/', $data['cne'])) {
$errors['cne'] = 'CNE requis (A-Z, 0-9, 6-20).';
}
if ($data['nom'] === '') { $errors['nom'] = 'Nom requis.';
}
if ($data['prenom'] === '') { $errors['prenom'] = 'Prenom requis.';
}
if ($data['email'] === '' ||!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
$errors['email'] = 'Attention Email invalide.';
}
if ($data['filiere_id'] <= 0 ||!$this->filiereDao->findById((int) $data['filiere_id'])) {
$errors['filiere_id'] = ' Attention Filière invalide.';
}
return $errors;
}
}