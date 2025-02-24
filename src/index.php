<?php
session_start();
require 'config.php';

$isConnected = isset($_SESSION['id']);
$dateActuelle = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

// Calculer la première date de la semaine en cours
$lundi = date("Y-m-d", strtotime('monday this week', strtotime($dateActuelle)));
$dimanche = date("Y-m-d", strtotime('sunday this week', strtotime($dateActuelle)));

// Navigation entre les semaines
$semainePrecedente = date("Y-m-d", strtotime("-1 week", strtotime($lundi)));
$semaineSuivante = date("Y-m-d", strtotime("+1 week", strtotime($lundi)));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2 class="my-3">Calendrier des Rendez-vous</h2>

        <!-- Afficher connexion ou déconnexion -->
        <?php if (!$isConnected): ?>
            <a href="login.php" class="btn btn-primary">Se connecter</a>
        <?php else: ?>
            <p>Connecté en tant que <strong><?= htmlspecialchars($_SESSION['nom']) ?></strong> <strong><?= htmlspecialchars($_SESSION['prenom']) ?></strong> 
                (<a href="disconect.php">Déconnexion</a>)
            </p>
        <?php endif; ?>

        <!-- Navigation entre les semaines -->
        <div class="d-flex justify-content-between my-3">
            <a href="index.php?date=<?= $semainePrecedente ?>" class="btn btn-outline-secondary">← Semaine précédente</a>
            <span class="fw-bold">Semaine du <?= date("d/m/Y", strtotime($lundi)) ?> au <?= date("d/m/Y", strtotime($dimanche)) ?></span>
            <a href="index.php?date=<?= $semaineSuivante ?>" class="btn btn-outline-secondary">Semaine suivante →</a>
        </div>

        <!-- Affichage du calendrier -->
        <table class="table table-bordered">
            <tr>
                <th>Jour</th>
                <?php for ($i = 0; $i < 7; $i++): ?>
                    <th><?= date("D d/m", strtotime("+$i day", strtotime($lundi))) ?></th>
                <?php endfor; ?>
            </tr>

            <!-- Créneaux horaires -->
            <?php for ($heure = 8; $heure <= 17; $heure++): ?>
                <tr>
                    <td><?= $heure ?>:00</td>
                    <?php for ($i = 0; $i < 7; $i++): ?>
                        <?php 
                        $date = date("Y-m-d", strtotime("+$i day", strtotime($lundi)));
                        $heureFormat = str_pad($heure, 2, "0", STR_PAD_LEFT) . ":00:00";

                        // Vérifier si le créneau est déjà réservé
                        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE date_rdv = ? AND heure_rdv = ?");
                        $stmt->execute([$date, $heureFormat]);
                        $estReserve = $stmt->fetch();
                        ?>
                        <td>
                            <?php if ($isConnected): ?>
                                <?php if ($estReserve): ?>
                                    <button class="btn btn-danger btn-sm" disabled>Réservé</button>
                                <?php else: ?>
                                    <a href="reserver.php?date=<?= $date ?>&heure=<?= $heureFormat ?>" class="btn btn-success btn-sm">Réserver</a>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-warning btn-sm">Connexion requise</a>
                            <?php endif; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </table>
    </div>
</body>
</html>
