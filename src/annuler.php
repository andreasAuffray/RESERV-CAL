<?php
require 'config.php';
require 'csrf.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Token CSRF invalide !");
    }

    require 'config.php';

    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ? AND user_id = ?");
    $stmt->execute([$_POST['reservation_id'], $_SESSION['id']]);

    header('Location: mes_rdv.php');
    exit();
}
?>
