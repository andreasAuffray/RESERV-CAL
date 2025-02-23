<?php
session_start();
include 'config.php';

$user_id = $_SESSION['id'];

// Vérification des paramètres
if (isset($_GET['date']) && isset($_GET['heure'])) {
    $date = $_GET['date'];
    $heure = $_GET['heure'];

    // Vérifier si le créneau est déjà réservé
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE date_rdv = ? AND heure_rdv = ?");
    $stmt->execute([$date, $heure]);

    if ($stmt->rowCount() > 0) {
        echo "Ce créneau est déjà réservé.";
        exit;
    }

    // Insérer la réservation
    $stmt = $pdo->prepare("INSERT INTO reservations (user_id, date_rdv, heure_rdv) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $date, $heure]);

    echo "Réservation confirmée pour le $date à $heure. <a href='index.php'>Retour</a>";
} else {
    echo "Paramètres invalides.";
}
?>
