<?php 
/** @var array $etudiants, array $filieres, int $filiereId, string $q, int $page, int $size, int $total, int $totalPages */ 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Étudiants</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        
        h2 {
            color: #333;
        }
        
        form {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        input, select, button {
            padding: 8px;
            margin: 5px;
        }
        
        table {
            width: 100%;
            background: white;
            border-collapse: collapse;
            border-radius: 5px;
            overflow: hidden;
        }
        
        th {
            background: #333;
            color: white;
            padding: 10px;
            text-align: left;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .pagination {
            margin-top: 20px;
        }
        
        .pagination a {
            padding: 5px 10px;
            background: white;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
        }
        
        .btn {
            background: #28a745;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h2>Étudiants</h2>
    
    <!-- Formulaire de recherche -->
    <form method="get" action="/etudiants">
        <input name="q" placeholder="Rechercher..." value="<?php echo htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <select name="filiere_id">
            <option value="">Toutes filières</option>
            <?php foreach ($filieres as $f): ?>
                <option value="<?php echo $f['id']; ?>" <?php echo ($filiereId == $f['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($f['code'] . ' - ' . $f['libelle']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="size" value="<?php echo $size ?? 10; ?>">
        <button type="submit">Filtrer</button>
    </form>

    <!-- Info -->
    <p>
        <strong>Total: <?php echo $total ?? 0; ?></strong> - Page <?php echo $page ?? 1; ?>/<?php echo $totalPages ?? 1; ?>
        <a href="/etudiants/create" class="btn" style="float:right;">+ Nouveau</a>
    </p>

    <?php if (empty($etudiants)): ?>
        <p>Aucun étudiant trouvé.</p>
    <?php else: ?>
        <!-- Tableau -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CNE</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Filière</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($etudiants as $e): ?>
                <tr>
                    <td><?php echo $e['id']; ?></td>
                    <td><?php echo htmlspecialchars($e['cne'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($e['nom'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($e['prenom'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($e['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars(($e['filiere_code'] ?? '') . ' - ' . ($e['filiere_libelle'] ?? '')); ?></td>
                    <td>
                        <a href="/etudiants/<?php echo $e['id']; ?>">Voir</a>
                        <a href="/etudiants/<?php echo $e['id']; ?>/edit">Éditer</a>
                        <form action="/etudiants/<?php echo $e['id']; ?>/delete" method="post" style="display:inline;">
                            <input type="hidden" name="_csrf" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">
                            <button type="submit" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="/etudiants?page=<?php echo $page-1; ?>&size=<?php echo $size; ?>&q=<?php echo urlencode($q ?? ''); ?>&filiere_id=<?php echo $filiere_id ?? ''; ?>">« Précédent</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="/etudiants?page=<?php echo $i; ?>&size=<?php echo $size; ?>&q=<?php echo urlencode($q ?? ''); ?>&filiere_id=<?php echo $filiere_id ?? ''; ?>" <?php echo ($i == $page) ? 'style="background:#007bff;color:white;"' : ''; ?>>
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="/etudiants?page=<?php echo $page+1; ?>&size=<?php echo $size; ?>&q=<?php echo urlencode($q ?? ''); ?>&filiere_id=<?php echo $filiere_id ?? ''; ?>">Suivant »</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>