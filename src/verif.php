<?php
require 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifier si le token existe dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM users WHERE verification_token = :token");
    $stmt->execute(['token' => $token]);
    $user = $stmt->fetch();

    if ($user) {
        // Activer le compte en modifiant la colonne `is_verified`
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = :token");
        $stmt->execute(['token' => $token]);

        echo "Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.";
    } else {
        echo "Le lien de vérification est invalide ou a déjà été utilisé.";
    }
} else {
    echo "Aucun token de vérification trouvé.";
}
?>
