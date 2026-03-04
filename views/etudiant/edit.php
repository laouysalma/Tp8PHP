<?php // views/etudiant/edit.php
/** @var array $e, array $filieres, array $errors, array $old */

function old(string $field, $default = ''): string {
    global $e, $old;
    if (!empty($old[$field])) return htmlspecialchars($old[$field], ENT_QUOTES, 'UTF-8');
    if (!empty($e[$field])) return htmlspecialchars($e[$field], ENT_QUOTES, 'UTF-8');
    return htmlspecialchars($default, ENT_QUOTES, 'UTF-8');
}
?>
<h2>Editer l'etudiant #<?= (int)($e['id'] ?? 0) ?></h2>

<form method="post" action="/etudiants/<?= (int)($e['id'] ?? 0) ?>/update">
    <!-- Champ cache CSRF unique pour proteger le formulaire -->
    <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

    <label>CNE
        <input name="cne" value="<?= old('cne') ?>" required>
        <?php if (!empty($errors['cne'])): ?><small class="error"><?= htmlspecialchars($errors['cne'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>
    </label>

    <label>Nom
        <input name="nom" value="<?= old('nom') ?>" required>
        <?php if (!empty($errors['nom'])): ?><small class="error"><?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>
    </label>

    <label>Prenom
        <input name="prenom" value="<?= old('prenom') ?>" required>
        <?php if (!empty($errors['prenom'])): ?><small class="error"><?= htmlspecialchars($errors['prenom'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>
    </label>

    <label>Email
        <input type="email" name="email" value="<?= old('email') ?>" required>
        <?php if (!empty($errors['email'])): ?><small class="error"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>
    </label>

    <label>Filiere
        <select name="filiere_id" required>
            <?php foreach ($filieres as $f): ?>
                <option value="<?= (int)$f['id'] ?>" <?= ((int)old('filiere_id') === (int)$f['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($f['code'].' — '.$f['libelle'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['filiere_id'])): ?><small class="error"><?= htmlspecialchars($errors['filiere_id'], ENT_QUOTES, 'UTF-8') ?></small><?php endif; ?>
    </label>

    <button type="submit">Enregistrer</button>
    <a role="button" class="secondary" href="/etudiants/<?= (int)($e['id'] ?? 0) ?>">Annuler</a>
</form>