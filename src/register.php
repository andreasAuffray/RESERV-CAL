<?php
session_start();
require 'config.php';

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

// Si le formulaire est soumis
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
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "L'adresse mail est déjà utilisée.";
        } else {
            // Hash du mot de passe
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);

            // Insérer l'utilisateur 
            $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, date_naissance, adresse, telephone, email, mot_de_passe) 
            VALUES (:nom, :prenom, :date_naissance, :adresse, :telephone, :email, :mot_de_passe)");

            $stmt->execute([
                'nom' => $nom, 
                'prenom' => $prenom, 
                'date_naissance' => $date_naissance,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'email' => $email,
                'mot_de_passe' => $mot_de_passe_hache
            ]);

            // Rediriger vers la page de connexion après la création du compte
            header('Location: login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Créer un compte</h2>

        <!-- Afficher l'erreur s'il y en a -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
            </div>
            
            <div class="mb-3">
                <label for="date_naissance" class="form-label">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" class="form-control" id="adresse" name="adresse" required>
            </div>
            
            <div class="mb-3">
                <label for="telephone" class="form-label">Telephone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" required>
            </div>

            <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                </div>

            <button type="submit" class="btn btn-primary">Créer le compte</button>
        </form>

        <p class="mt-3">Déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>
</body>
</html>
