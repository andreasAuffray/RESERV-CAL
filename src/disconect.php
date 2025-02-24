<?php
session_start();

if (!isset($_SESSION['id'])) {
    header('Location: login.php');  
    exit();
}


session_unset();

// Détruire la session
session_destroy();


header('Location: index.php');  
exit();
?>