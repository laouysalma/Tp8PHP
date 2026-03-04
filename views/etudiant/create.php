<h2>Ajouter un etudiant</h2>

<form method="post" action="/etudiants/store">
    <!-- Champ cache CSRF unique -->
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

    <?php
    $fields = ['cne' => 'CNE', 'nom' => 'Nom', 'prenom' => 'Prenom', 'email' => 'Email'];
    foreach ($fields as $key => $label):
    ?>
        <label><?= $label ?>
            <input name="<?= $key ?>" value="<?= htmlspecialchars($old[$key] ?? '', ENT_QUOTES, 'UTF-8') ?>" required <?= $key === 'email' ? 'type="email"' : '' ?>>
        </label>
        <?php if (!empty($errors[$key])): ?>
            <small class="error"><?= htmlspecialchars($errors[$key], ENT_QUOTES, 'UTF-8') ?></small>
        <?php endif; ?>
    <?php endforeach; ?>

    <label>Filiere
        <select name="filiere_id" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($filieres as $f): ?>
                <option value="<?= (int)$f['id'] ?>" <?= (isset($old['filiere_id']) && (int)$old['filiere_id'] === (int)$f['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f['code'] . ' — ' . $f['libelle'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <?php if (!empty($errors['filiere_id'])): ?>
        <small class="error"><?= htmlspecialchars($errors['filiere_id'], ENT_QUOTES, 'UTF-8') ?></small>
    <?php endif; ?>

    <button type="submit">Creer</button>
    <a role="button" class="secondary" href="/etudiants">Annuler</a>
</form>