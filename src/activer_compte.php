<?php
    require 'config.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code= htmlspecialchars($_POST['code']);

        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE ((verification_token = :verification_token) and (is_verified = 0 ))");
        $stmt->execute(['verification_token' => $code]);
        header('Location: login.php');
         
    }

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activation de compte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .activation-container {
            max-width: 450px;
            margin: auto;
            padding-top: 50px;
        }
        .card {
            border-radius: 10px;
            padding: 30px;
            text-align: center;
        }
        .card h2 {
            font-size: 22px;
            font-weight: bold;
            color: #343a40;
        }
        .card p {
            color: #6c757d;
        }
        .btn-activate {
            background-color: #198754;
            color: white;
            font-size: 16px;
            padding: 10px;
            border-radius: 5px;
            width: 100%;
            border: none;
        }
        .btn-activate:hover {
            background-color: #157347;
        }
    </style>
</head>

<body>

    <div class="container activation-container">
        <div class="card shadow-lg">
            <h2>Activation de votre compte</h2>
            <p>Veuillez cliquer sur le bouton ci-dessous pour activer votre compte.</p>

            <form method="POST">
                <div class="mb-3">
                    <label>Code</label>
                    <input type="texte" name="code" class="form-control" required>
                 </div>
                <input type="hidden" name="csrf_token">
                <button type="submit" class="btn-activate mt-3">Activer mon compte</button>
            </form>
        </div>
    </div>

</body>
</html>
