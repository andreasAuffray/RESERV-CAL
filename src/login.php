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
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-sm" style="width: 300px;">
            <h3 class="text-center mb-4">Se connecter</h3>
            
            <!-- Affichage du message d'erreur -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Se connecter</button>
            </form>

            <p class="mt-3 text-center">Pas de compte ? <a href="register.php">S'inscrire</a></p>
        </div>
    </div>
</body>
</html>
