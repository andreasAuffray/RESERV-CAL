<?php
require 'config.php';
require 'csrf.php';
include 'navbar.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si l'extension intl est activée
if (!class_exists('IntlDateFormatter')) {
    die("L'extension PHP 'intl' est requise pour afficher les dates en français.");
}

// Récupérer les rendez-vous de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ?");
$stmt->execute([$_SESSION['id']]);
$rdvs = $stmt->fetchAll();

// Configuration du formatteur de date
$formatter = new IntlDateFormatter(
    'fr_FR', 
    IntlDateFormatter::FULL, 
    IntlDateFormatter::NONE,
    null,
    IntlDateFormatter::GREGORIAN,
    'EEEE d MMMM yyyy' // Format "Jour Numéro Mois Année"
);
?>

<div class="container mt-5">
    <h2>Mes Rendez-vous</h2>
    <table class="table table-striped">
        <tr>
            <th>Date</th>
            <th>Heure</th>
            <th>Action</th>
        </tr>
        <?php foreach ($rdvs as $rdv): ?>
            <?php
            // Convertir la date avec IntlDateFormatter
            $timestamp = strtotime($rdv['date_rdv']);
            $dateFormatee = $formatter->format($timestamp);
            ?>
            <tr>
                <td><?= ucfirst($dateFormatee) ?></td>
                <td><?= htmlspecialchars($rdv['heure_rdv']) ?></td>
                <td>
                    <form action="annuler.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="reservation_id" value="<?= $rdv['id'] ?>"> 
                        <button type="submit" class="btn btn-danger">Annuler</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

