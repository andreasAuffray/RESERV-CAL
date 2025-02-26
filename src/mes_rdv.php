<?php
require 'config.php';
require 'csrf.php';
include 'navbar.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = ?");
$stmt->execute([$_SESSION['id']]);
$rdvs = $stmt->fetchAll();
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
            <tr>
                <td><?= htmlspecialchars($rdv['date_rdv']) ?></td>
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
</body>
</html>