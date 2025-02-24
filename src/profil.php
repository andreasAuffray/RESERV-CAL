<?php
session_start();
require 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['id']]);
$user = $stmt->fetch();  // Notez qu'on utilise fetch() et non fetchAll()

// Vérifier si l'utilisateur existe
if (!$user) {
    // Si l'utilisateur n'existe pas, rediriger vers la page de connexion
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier que les champs sont remplis
    if (empty($nom) || empty($prenom) || empty($date_naissance) || empty($adresse) || empty($telephone) || empty($email) || empty($mot_de_passe)) {
        $error = "Tous les champs doivent être remplis.";
    } else {
        // Hash du mot de passe
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        // Mise à jour des informations de l'utilisateur
        $stmt = $pdo->prepare("UPDATE users SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, adresse = :adresse, 
            telephone = :telephone, email = :email, mot_de_passe = :mot_de_passe WHERE id = :id");

        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'date_naissance' => $date_naissance,
            'adresse' => $adresse,
            'telephone' => $telephone,
            'email' => $email,
            'mot_de_passe' => $mot_de_passe_hache,
            'id' => $_SESSION['id']
        ]);

        // Rediriger vers la page du profil après la mise à jour
        header('Location: profil.php');
        exit();
    }
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
        <h2>Profil de l'utilisateur</h2>

        <!-- Afficher l'erreur s'il y en a -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- Formulaire de mise à jour du profil -->
        <form method="POST" action="">
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
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user['telephone']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" value="<?= htmlspecialchars($user['mot_de_passe']) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>

    <div class="container mt-4">
        
        <button type="" class="btn btn-secondary"><a href="disconect.php">Déconnexion</a></button>
        
    </div>

    <div class="container mt-4">
            <button type="submit" class="btn btn-danger">Supprimer le compte</button>
    </div>

    <div class="container mt-4">
        
        <button type="" class="btn btn-secondary"><a href="index.php">Prendre rendez_vous</a></button>
        
    </div>

    <div class="container mt-4">
        
        <button type="" class="btn btn-secondary"><a href="mes_rdv.php">Mes reservations</a></button>
        
    </div>

</body>
</html>
