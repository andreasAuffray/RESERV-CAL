<?php
require 'config.php';
require 'csrf.php';
include 'navbar.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Token CSRF invalide !");
    }

    // Récupérer les informations du formulaire
    $date_rdv = $_POST['date_rdv'];  // La date
    $heure_rdv = $_POST['heure_rdv']; // L'heure

    // Vérifier si ce créneau est déjà réservé
    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE date_rdv = :date_rdv AND heure_rdv = :heure_rdv");
    $stmt->execute(['date_rdv' => $date_rdv, 'heure_rdv' => $heure_rdv]);
    $existingRdv = $stmt->fetch();

    if ($existingRdv) {
        // Si le créneau est déjà réservé, afficher un message d'erreur
        $error = "Ce créneau est déjà réservé. Veuillez en choisir un autre.";
    } else {
        // Si le créneau est libre, procéder à l'insertion de la réservation
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, date_rdv, heure_rdv) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['id'], $date_rdv, $heure_rdv]);

        $success = "Votre réservation a été effectuée avec succès.";
    }
}
?>

<div class="container mt-5">
    <h2>Réserver un créneau</h2>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="mb-3">
            <label for="date_rdv" class="form-label">Date du rendez-vous</label>
            <input type="date" class="form-control" id="date_rdv" name="date_rdv" required>
        </div>

        <div class="mb-3">
            <label for="heure_rdv" class="form-label">Heure du rendez-vous</label>
            <input type="time" class="form-control" id="heure_rdv" name="heure_rdv" required>
        </div>

        <button type="submit" class="btn btn-primary">Réserver</button>
    </form>
</div>

