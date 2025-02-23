<?php
session_start();
include 'config.php'; // Connexion à la base de données

// Récupérer la semaine demandée (ou la semaine actuelle)
$semaine = isset($_GET['semaine']) ? $_GET['semaine'] : date("W");
$annee = isset($_GET['annee']) ? $_GET['annee'] : date("Y");

// Déterminer le premier jour de la semaine sélectionnée
$dateDebut = new DateTime();
$dateDebut->setISODate($annee, $semaine);
$dateFin = clone $dateDebut;
$dateFin->modify('+6 days');

// Liste des jours de la semaine
$jours = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];

// Génération des boutons de navigation
$semainePrecedente = $semaine - 1;
$semaineSuivante = $semaine + 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Calendrier des Réservations</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
        h2 { color: #333; }
        .navigation { margin-bottom: 20px; }
        .navigation a { padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .navigation a:hover { background: #2980b9; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f8f8f8; }
        .btn-reserver { display: block; padding: 5px; background: #2ecc71; color: white; text-decoration: none; border-radius: 5px; margin-top: 5px; }
        .btn-reserver:hover { background: #27ae60; }
    </style>
</head>
<body>

<h2>Semaine du <?= $dateDebut->format("d/m/Y") ?> au <?= $dateFin->format("d/m/Y") ?></h2>

<div class="navigation">
    <a href="?semaine=<?= $semainePrecedente ?>&annee=<?= $annee ?>">⏪ Semaine Précédente</a>
    <a href="?semaine=<?= date("W") ?>&annee=<?= date("Y") ?>">📅 Aujourd'hui</a>
    <a href="?semaine=<?= $semaineSuivante ?>&annee=<?= $annee ?>">Semaine Suivante ⏩</a>
</div>

<table>
    <tr>
        <th>Jour</th>
        <?php foreach (range(8, 17) as $heure) { echo "<th>$heure:00</th>"; } ?>
    </tr>
    <?php
    foreach ($jours as $index => $jourNom) {
        $date = $dateDebut->format("Y-m-d");
        echo "<tr><td><b>$jourNom</b><br><small>$date</small></td>";

        foreach (range(8, 17) as $heure) {
            $heureFormat = str_pad($heure, 2, "0", STR_PAD_LEFT) . ":00:00";

            // Vérifier si le créneau est réservé
            $stmt = $pdo->prepare("SELECT * FROM reservations WHERE date_rdv = ? AND heure_rdv = ?");
            $stmt->execute([$date, $heureFormat]);
            $estReserve = $stmt->rowCount() > 0;

            if ($estReserve) {
                echo "<td style='background: #e74c3c; color: white;'>Réservé</td>";
            } else {
                echo "<td><a href='reserver.php?date=$date&heure=$heureFormat' class='btn-reserver'>Réserver</a></td>";
            }
        }
        echo "</tr>";
        $dateDebut->modify('+1 day');
    }
    ?>
</table>

</body>
</html>
