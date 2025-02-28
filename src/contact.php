<?php
require 'config.php'; 
require 'csrf.php'; 
require 'navbar.php';

// Inclusion des fichiers de PHPMailer
require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\Exception.php';  
require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';  
require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\SMTP.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validation du token CSRF
        if (!verifyCsrfToken($_POST['csrf_token'])) {
            die("Token CSRF invalide !");
        }

        // Assainir et récupérer les données
        $nom = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['email']); // Assainir l'email avec htmlspecialchars
        $message = htmlspecialchars($_POST['message']);

        // Configurer PHPMailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'webreservcall@gmail.com';
        $mail->Password = 'axsj xgmu xpib isoo';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Options SMTP pour ignorer les erreurs SSL (optionnel)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('webreservcall@gmail.com', 'reservcall');
        $mail->addAddress('webreservcall@gmail.com', 'reservcall');
        $mail->isHTML(true);
        $mail->Subject = 'Contact';
        $mail->Body    = "Adresse mail : ".$email."<br><br> Message : ".$message;

        // Envoyer l'email
        $mail->send();
        echo 'E-mail envoyé avec succès.';
    } catch (Exception $e) {
        // Gérer les erreurs de PHPMailer
        echo "L'envoi de l'e-mail a échoué. Erreur Mailer: {$mail->ErrorInfo}";
    } catch (Exception $e) {
        // Gérer les erreurs de validation du token CSRF
        echo "Erreur CSRF: " . $e->getMessage();
    }
}
?>


<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm p-4">
            <h2 class="text-center text-primary mb-4">Nous Contacter</h2>
            <p class="text-center text-muted">Remplissez ce formulaire et nous vous répondrons rapidement.</p>

            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Adresse Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Votre Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>

                <!-- Ajouter le token CSRF dans le formulaire -->
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">

                <button type="submit" class="btn btn-primary w-100">Envoyer</button>
            </form>
        </div>
    </div>

