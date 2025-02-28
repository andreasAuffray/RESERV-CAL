<?php
session_start();
require 'config.php';
require 'csrf.php';
require 'navbar.php';

require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\Exception.php';  
require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';  
require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\SMTP.php';  

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'])) {
        die("Token CSRF invalide !");
    }

    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $date_naissance = $_POST['date_naissance'];
    $adresse = htmlspecialchars($_POST['adresse']);
    $telephone = htmlspecialchars($_POST['telephone']);
    $email = $_POST['email']; 
    $mot_de_passe = $_POST['mot_de_passe'];

    if (empty($nom) || empty($prenom) || empty($date_naissance) || empty($adresse) || empty($telephone) || empty($email) || empty($mot_de_passe)) {
        $error = "Tous les champs doivent être remplis.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "L'adresse email est déjà utilisée.";
        } else {
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);
            $activation_code = bin2hex(random_bytes(16));

            $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, date_naissance, adresse, telephone, email, mot_de_passe, is_verified, verification_token) 
            VALUES (:nom, :prenom, :date_naissance, :adresse, :telephone, :email, :mot_de_passe, 0, :verification_token)");

            $stmt->execute([
                'nom' => $nom, 
                'prenom' => $prenom, 
                'date_naissance' => $date_naissance,
                'adresse' => $adresse,
                'telephone' => $telephone,
                'email' => $email,
                'mot_de_passe' => $mot_de_passe_hache,
                'verification_token' => $activation_code
            ]);

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'webreservcall@gmail.com';
                $mail->Password = 'axsj xgmu xpib isoo';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Activer le débogage

                // Ajouter les options SMTP
                $mail->SMTPOptions = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    )
                );

                $mail->setFrom('webreservcall@gmail.com', 'Reserv call');
                $mail->addAddress($email, $nom);

                $mail->isHTML(true);
                $mail->Subject = 'Activation de votre compte';
                $mail->Body    = 'Bonjour, <br><br>Votre compte a cree avec success. Cliquez sur le lien ci-dessous pour activer votre compte et inseré le code suivant <br>'.$activation_code.'  : <br><br><a href="http://githubweb/RESERV-CAL/src/activer_compte.php?token='.$activation_code.'">Activer mon compte</a><br><br>Cordialement,<br>Reserv call';

                $mail->send();
                echo 'E-mail envoyé avec succès.';
            } catch (Exception $e) {
                echo "L'envoi de l'e-mail a échoué. Erreur Mailer: {$mail->ErrorInfo}";
            }
        }
    }
}
?>


<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="card-title">Créer un compte</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>

                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>

                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                    </div>

                    <div class="mb-3">
                        <label for="adresse" class="form-label">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" required>
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Numéro de téléphone</label>
                        <input type="text" class="form-control" id="telephone" name="telephone" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Créer un compte</button>
                    <a href="login.php" class="btn btn-link">Déjà un compte ?</a>
                </form>
            </div>
        </div>
    </div>
