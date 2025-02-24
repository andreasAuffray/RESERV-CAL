<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction(); // Démarrer une transaction pour éviter les erreurs

    try {
        // Suppression des réservations associées
        $stmt = $pdo->prepare("DELETE FROM reservations WHERE user_id = :id;");
        $stmt->execute(['id' => $_SESSION['id']]);

        // Suppression de l'utilisateur
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id;");
        $stmt->execute(['id' => $_SESSION['id']]);

        // Valider la transaction
        $pdo->commit();

        session_destroy();
        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack(); // Annuler la transaction en cas d'erreur
        echo "Erreur : " . $e->getMessage();
    }
}
?>
