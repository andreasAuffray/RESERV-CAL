<?php
session_start();
require 'config.php';
require 'csrf.php';
require 'navbar.php';

$error = ""; // Initialiser une variable d'erreur comme une chaîne vide

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Token CSRF invalide !");
    }

    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    // Vérifier si l'utilisateur existe dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Vérifier si le mot de passe est correct
        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            // Vérifier si l'utilisateur est activé
            if ($user['is_verified'] == 1) {
                // Si activé, on connecte l'utilisateur
                $_SESSION['id'] = $user['id'];
                header('Location: profil.php'); 
                exit();
            } else {
                // Si le compte n'est pas activé
                $error = "Votre compte n'est pas encore activé. Veuillez vérifier votre e-mail.";
            }
        } else {
            // Mot de passe incorrect
            $error = "Identifiants incorrects.";
        }
    } else {
        // Utilisateur non trouvé
        $error = "Identifiants incorrects.";
    }
}
?>

<div class="container mt-5">
    <h2>Connexion</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Se connecter</button>
        <a href="register.php" class="btn btn-link">Créer un compte</a>
    </form>
</div>
</body>
</html>
