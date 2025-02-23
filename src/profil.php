<?php
session_start();
require 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les données de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);

    // Vérifier si un mot de passe a été renseigné pour mise à jour
    if (!empty($_POST['mot_de_passe'])) {
        $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, adresse = ?, telephone = ?, mot_de_passe = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $adresse, $telephone, $mot_de_passe, $_SESSION['id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, email = ?, adresse = ?, telephone = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $adresse, $telephone, $_SESSION['id']]);
    }

    header('Location: profil.php');
}
?>





<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Profil</h2>

        <!-- Afficher l'erreur s'il y en a -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user[0]['nom']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user[0]['prenom']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="date_naissance" class="form-label">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($user[0]['date_naissance']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($user[0]['adresse']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="telephone" class="form-label">Telephone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user[0]['telephone']) ?>" required>
            </div>

            <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user[0]['email']) ?>" required>
            </div>

            <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" value="<?= htmlspecialchars($user[0]['mot_de_passe']) ?>" required>
                </div>

            <button type="submit" class="btn btn-primary">Modifier</button>

            
        </form>
        

        
        
    </div>

    <div class="container mt-4">
            <form action="disconect.php" method="POST">
                <button type="submit" class="btn btn-btn btn-secondary">Deconexion</button>
            </form>
        </div>

        <div class="container mt-4">
            <form action="delete.php" method="POST">
                <button type="submit" class="btn btn-danger">Supprimer Compte</button>
            </form>
        </div>
</body>
</html>
