<?php /** @var array $errors, array $old */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        
        form {
            background: white;
            padding: 30px;
            border-radius: 5px;
            width: 300px;
        }
        
        h2 {
            text-align: center;
            margin-top: 0;
        }
        
        .error {
            color: red;
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
        }
        
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
        }
        
        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <form method="post" action="/login">
        <h2>Connexion</h2>
        
        <?php if (!empty($errors['global'])): ?>
            <div class="error"><?= htmlspecialchars($errors['global']) ?></div>
        <?php endif; ?>
        
        <input type="hidden" name="_csrf" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
        
        <label>Nom d'utilisateur</label>
        <input type="text" name="username" required value="<?= htmlspecialchars($old['username'] ?? '') ?>">
        
        <label>Mot de passe</label>
        <input type="password" name="password" required>
        
        <button type="submit">Se connecter</button>
    </form>
</body>
</html>