<?php
require 'config.php';
session_start();


    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id ");
    $stmt->execute(['id' => $_SESSION['id']]);
    $user = $stmt->fetchAll();


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom= $_POST['nom'];
        $prenom= $_POST['prenom'];
        $date_naissance= $_POST['date_naissance'];
        $adresse= $_POST['adresse'];
        $telephone= $_POST['telephone'];
        $email = $_POST['email'];
        $mot_de_passe = $_POST['mot_de_passe'];
    
        // Vérifier que les champs sont remplis
        if ( empty($nom) || empty($prenom) || empty($date_naissance) || empty($adresse) || empty($telephone) || empty($email) || empty($mot_de_passe)) {
            $error = "Tous les champs doivent être remplis.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $existingUser = $stmt->fetch();
    
            if ($existingUser) {
                $error = "L'adresse mail est déjà utilisé.";
            } else {
                // Modifier
                $stmt = $pdo->prepare("UPDATE users SET nom=:nom, prenom=:prenom,date_naissance=:date_naissance,
                adresse=:adresse,telephone=:telephone,email=:email,mot_de_passe=:mot_de_passe WHERE id=:id");
    
                $stmt->execute(['nom' => $nom, 'prenom' => $prenom, 'date_naissance'=> $date_naissance,'adresse' => $adresse,'telephone' => $telephone,'email' => $email,'mot_de_passe' => $mot_de_passe, 'id'=>$_SESSION['id'] ]);
                header('Location: profil.php');

               
            }
        }
    }
    ?>




<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Profil</h2>

        <!-- Afficher l'erreur s'il y en a -->
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user[0]['nom']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user[0]['prenom']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="date_naissance" class="form-label">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($user[0]['date_naissance']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="adresse" class="form-label">Adresse</label>
                <input type="text" class="form-control" id="adresse" name="adresse" value="<?= htmlspecialchars($user[0]['adresse']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="telephone" class="form-label">Telephone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" value="<?= htmlspecialchars($user[0]['telephone']) ?>" required>
            </div>

            <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user[0]['email']) ?>" required>
            </div>

            <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe</label>
                    <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" value="<?= htmlspecialchars($user[0]['mot_de_passe']) ?>" required>
                </div>

            <button type="submit" class="btn btn-primary">Modifier</button>

            
        </form>
        <form action="delete.php" method="POST">
            <button type="submit" class="btn btn-danger">Supprimer Compte</button>
        </form>

        
    </div>
</body>
</html>
