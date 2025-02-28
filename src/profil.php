<?php
session_start();
require 'config.php';
require 'csrf.php';
include 'navbar.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];
$error = ''; // Initialisation de la variable $error
$success = ''; // Initialisation de la variable $success

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Token CSRF invalide !");
    }

    // Récupérer les valeurs du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $date_naissance = $_POST['date_naissance'];
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérification du mot de passe et des champs
    if (empty($nom) || empty($prenom) || empty($date_naissance) || empty($adresse) || empty($telephone) || empty($email)) {
        $error = "Tous les champs doivent être remplis.";
    } else {
        if (!empty($mot_de_passe)) {
            // Si le mot de passe est changé, on le hache
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, date_naissance = ?, adresse = ?, telephone = ?, email = ?, mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $date_naissance, $adresse, $telephone, $email, $mot_de_passe_hache, $userId]);
        } else {
            // Si le mot de passe n'est pas changé
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ?, date_naissance = ?, adresse = ?, telephone = ?, email = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $date_naissance, $adresse, $telephone, $email, $userId]);
        }

        $success = "Vos informations ont été mises à jour avec succès.";
    }
}

// Récupérer les informations actuelles de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
?>

<div class="container mt-5">
    <h2>Mon profil</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">

        <div class="mb-3">
            <label for="nom" class="form-label">Nom</label>
            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="date_naissance" class="form-label">Date de naissance</label>
            <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= $user['date_naissance'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="adresse" class="form-label">Adresse</label>
            <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="telephone" class="form-label">Numéro de téléphone</label>
            <input type="tel" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="mot_de_passe" class="form-label">Mot de passe (laisser vide si inchangé)</label>
            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
        </div>

        <button type="submit" class="btn btn-primary w-100">Mettre à jour le profil</button>
    </form>

    <form action="delete.php" method="POST" class="mt-4">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
        <button type="submit" class="btn btn-danger w-100">Supprimer mon compte</button>
    </form>
</div>

