<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = :reservation_id AND user_id = :user_id");
        $stmt->execute([
            'reservation_id' => $reservation_id,
            'user_id' => $_SESSION['id']
        ]);

    
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Requête invalide.";
}
header('Location: index.php');

?>
