<?php
require 'config.php';
require 'csrf.php';
require 'navbar.php';

require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\Exception.php';  
require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\PHPMailer.php';  
require 'C:\Users\andre\Documents\PHPMailer-master\PHPMailer-master\src\SMTP.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    $nom = htmlspecialchars($_POST['name']);
    $email = $_POST['email'];
    $message= htmlspecialchars($_POST['message']) ;
    
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

        $mail->setFrom('webreservcall@gmail.com', 'reservcall');
        $mail->addAddress('webreservcall@gmail.com', 'reservcall');

        $mail->isHTML(true);
        $mail->Subject = 'Contact';
        $mail->Body    = "Adresse mail : ".$email."<br><br> Message : ".$message;

        $mail->send();
        echo 'E-mail envoyé avec succès.';
    } catch (Exception $e) {
        echo "L'envoi de l'e-mail a échoué. Erreur Mailer: {$mail->ErrorInfo}";
    }



}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

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

                <input type="hidden" name="csrf_token">

                <button type="submit" class="btn btn-primary w-100">Envoyer</button>
            </form>
        </div>
    </div>
</body>
</html>
