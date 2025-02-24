<?php
session_start();
require 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Récupérer les rendez-vous de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ?");
$stmt->execute([$_SESSION['id']]);
$rdvs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes rendez-vous</title>
</head>
<body>
    <h2>Mes Rendez-vous</h2>
    <table>
        <tr>
            <th>Date</th>
            <th>Heure</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rdvs as $rdv): ?>
            <tr>
                <td><?= htmlspecialchars($rdv['date_rdv']) ?></td>
                <td><?= htmlspecialchars($rdv['heure_rdv']) ?></td>
                <td><a href="annuler.php?id=<?= $rdv['id'] ?>">Annuler</a></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
