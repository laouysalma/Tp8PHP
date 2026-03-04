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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        /* Form styles */
        form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        input[type="text"], select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            flex: 1;
            min-width: 200px;
        }

        button[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Info bar */
        p {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        a[role="button"] {
            background-color: #28a745;
            color: white;
            text-decoration: none;
            padding: 8px 20px;
            border-radius: 4px;
            display: inline-block;
        }

        a[role="button"]:hover {
            background-color: #218838;
        }

        /* Table styles */
        table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        th {
            padding: 12px;
            text-align: left;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Action links */
        td a {
            color: #007bff;
            text-decoration: none;
            margin-right: 10px;
        }

        td a:hover {
            text-decoration: underline;
        }

        td form {
            display: inline;
            padding: 0;
            margin: 0;
            box-shadow: none;
            background: none;
        }

        td button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 4px 12px;
            border-radius: 3px;
            cursor: pointer;
        }

        td button:hover {
            background-color: #c82333;
        }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .pagination a {
            padding: 8px 12px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #007bff;
        }

        .pagination a:hover {
            background-color: #e7f1ff;
        }

        .pagination a[aria-current="page"] {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <h2>Étudiants</h2>
    
    <!-- Filter form -->
    <form method="get" action="/etudiants">
        <input name="q" placeholder="Rechercher (nom, prénom, email, CNE)" 
               value="<?php echo htmlspecialchars($q ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <select name="filiere_id">
            <option value="">Toutes filières</option>
            <?php foreach ($filieres as $f): ?>
                <option value="<?php echo (int)$f['id']; ?>" 
                    <?php echo (isset($filiereId) && (int)$filiereId === (int)$f['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($f['code'] . ' — ' . $f['libelle'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="size" value="<?php echo (int)($size ?? 10); ?>">
        <button type="submit">Filtrer</button>
    </form>

    <!-- Info bar -->
    <p>
        <span>Total: <?php echo (int)($total ?? 0); ?> — Page <?php echo (int)($page ?? 1); ?>/<?php echo (int)($totalPages ?? 1); ?></span>
        <a role="button" href="/etudiants/create">Nouveau</a>
    </p>

    <?php if (empty($etudiants)): ?>
        <p>Aucun étudiant.</p>
    <?php else: ?>
        <!-- Students table -->
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
                    <td><?php echo (int)$e['id']; ?></td>
                    <td><?php echo htmlspecialchars($e['cne'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($e['nom'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($e['prenom'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($e['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <?php 
                        $filiereCode = $e['filiere_code'] ?? '';
                        $filiereLibelle = $e['filiere_libelle'] ?? '';
                        echo htmlspecialchars($filiereCode . ($filiereCode && $filiereLibelle ? ' — ' : '') . $filiereLibelle, ENT_QUOTES, 'UTF-8'); 
                        ?>
                    </td>
                    <td>
                        <a href="/etudiants/<?php echo (int)$e['id']; ?>">Voir</a>
                        <a href="/etudiants/<?php echo (int)$e['id']; ?>/edit">Éditer</a>
                        <form action="/etudiants/<?php echo (int)$e['id']; ?>/delete" method="post" style="display:inline" 
                              onsubmit="return confirm('Supprimer ?');">
                            <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php 
        $base = '/etudiants?size=' . (int)($size ?? 10) . 
                '&q=' . urlencode($q ?? '') . 
                '&filiere_id=' . (int)($filiereId ?? 0) . 
                '&page='; 
        ?>
        <nav class="pagination">
            <?php if (isset($page) && $page > 1): ?>
                <a href="<?php echo $base . ($page - 1); ?>">« Préc.</a>
            <?php endif; ?>
            
            <?php for ($p = 1; $p <= ($totalPages ?? 1); $p++): ?>
                <a href="<?php echo $base . $p; ?>" 
                   <?php echo (isset($page) && $p == $page) ? 'aria-current="page"' : ''; ?>>
                    <?php echo $p; ?>
                </a>
            <?php endfor; ?>
            
            <?php if (isset($page, $totalPages) && $page < $totalPages): ?>
                <a href="<?php echo $base . ($page + 1); ?>">Suiv. »</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</body>
</html>