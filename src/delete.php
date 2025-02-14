<?php
    session_start();
        require 'config.php';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=:id;");
        $stmt->execute(['id' => $_SESSION['id']]);
        $user = $stmt->fetch();

        header('Location: login.php');
    }
?>