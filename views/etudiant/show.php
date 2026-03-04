<h2>Etudiant #<?= (int)$e['id'] ?></h2>

<ul>
  <li>CNE : <?= htmlspecialchars($e['cne'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>Nom : <?= htmlspecialchars($e['nom'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>Prenom : <?= htmlspecialchars($e['prenom'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>Email : <?= htmlspecialchars($e['email'], ENT_QUOTES, 'UTF-8') ?></li>
  <li>Filiere : <?= htmlspecialchars($e['filiere_code'].' — '.$e['filiere_libelle'], ENT_QUOTES, 'UTF-8') ?></li>
</ul>

<p>
  <a role="button" href="/etudiants/<?= (int)$e['id'] ?>/edit">Editer</a>
  <a role="button" class="secondary" href="/etudiants">Retour</a>

  <!-- Formulaire POST suppression avec CSRF -->
  <form action="/etudiants/<?= (int)$e['id'] ?>/delete" method="post" style="display:inline" onsubmit="return confirm('Supprimer ?');">
      <input type="hidden" name="_csrf" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
      <button type="submit" class="contrast">Supprimer</button>
  </form>
</p>