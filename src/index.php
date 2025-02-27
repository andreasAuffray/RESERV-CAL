<?php
session_start();
require 'config.php';
require 'csrf.php';
include 'navbar.php';

// Traitement de la réservation si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Token CSRF invalide !");
    }

    if (isset($_SESSION['id'])) {
        $date = $_POST['date'];
        $heure = $_POST['heure'];

        // Insertion de la réservation dans la base de données
        $stmt = $pdo->prepare("INSERT INTO reservations (user_id, date_rdv, heure_rdv) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['id'], $date, $heure]);

        // Redirection vers la page des rendez-vous
        header('Location: mes_rdv.php');
        exit(); // Assurez-vous de sortir immédiatement après la redirection
    } else {
        echo '<div class="alert alert-danger">Vous devez être connecté pour réserver un créneau.</div>';
    }
}

$isConnected = isset($_SESSION['id']);
$dateActuelle = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

$lundi = date("Y-m-d", strtotime('monday this week', strtotime($dateActuelle)));
$dimanche = date("Y-m-d", strtotime('sunday this week', strtotime($dateActuelle)));

$semainePrecedente = date("Y-m-d", strtotime("-1 week", strtotime($lundi)));
$semaineSuivante = date("Y-m-d", strtotime("+1 week", strtotime($lundi)));

// Tableau des jours de la semaine en français
$joursFrancais = ["Lun", "Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"];
?>

<div class="container mt-5">
    <h2 class="text-center">Calendrier des Rendez-vous</h2>

    <div class="d-flex justify-content-between my-3">
        <a href="index.php?date=<?= $semainePrecedente ?>" class="btn btn-outline-secondary">← Semaine précédente</a>
        <span class="fw-bold">Semaine du <?= date("d/m/Y", strtotime($lundi)) ?> au <?= date("d/m/Y", strtotime($dimanche)) ?></span>
        <a href="index.php?date=<?= $semaineSuivante ?>" class="btn btn-outline-secondary">Semaine suivante →</a>
    </div>

    <table class="table table-bordered">
        <tr>
            <th>Jour</th>
            <?php for ($i = 0; $i < 7; $i++): ?>
                <th><?= $joursFrancais[date("N", strtotime("+$i day", strtotime($lundi))) - 1] . " " . date("d/m", strtotime("+$i day", strtotime($lundi))) ?></th>
            <?php endfor; ?>
        </tr>

        <?php for ($heure = 8; $heure <= 17; $heure++): ?>
            <tr>
                <td><?= $heure ?>:00</td>
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <?php 
                    $date = date("Y-m-d", strtotime("+$i day", strtotime($lundi)));
                    $heureFormat = str_pad($heure, 2, "0", STR_PAD_LEFT) . ":00:00";

                    // Comparaison de la date et heure du créneau avec l'heure actuelle
                    $currentDateTime = new DateTime();
                    $slotDateTime = new DateTime("$date $heureFormat");

                    // Vérification si le créneau est dans le passé
                    $isPastSlot = $slotDateTime < $currentDateTime;

                    // Vérification si le créneau est réservé
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE date_rdv = ? AND heure_rdv = ?");
                    $stmt->execute([$date, $heureFormat]);
                    $estReserve = $stmt->fetchColumn();
                    ?>
                    <td>
                        <?php if ($isPastSlot): ?>
                            <!-- Affichage du créneau passé pour tout le monde -->
                            <button class="btn btn-secondary btn-sm" disabled>Passé</button>

                        <?php elseif ($isConnected): ?>
                            <!-- Si l'utilisateur est connecté -->
                            <?php if ($estReserve): ?>
                                <button class="btn btn-danger btn-sm" disabled>Réservé</button>
                            <?php else: ?>
                                <!-- Formulaire de réservation -->
                                <form action="index.php" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="date" value="<?= $date ?>">
                                    <input type="hidden" name="heure" value="<?= $heureFormat ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Réserver</button>
                                </form>
                            <?php endif; ?>

                        <?php else: ?>
                            <!-- Si l'utilisateur n'est pas connecté, on garde le bouton "Réserver" mais il redirige vers login -->
                            <form action="login.php" method="GET">
                                <input type="hidden" name="redirect" value="index.php?date=<?= $date ?>&heure=<?= $heureFormat ?>">
                                <button type="submit" class="btn btn-success btn-sm">Réserver</button>
                            </form>

                        <?php endif; ?>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>
</div>

</body>
</html>
