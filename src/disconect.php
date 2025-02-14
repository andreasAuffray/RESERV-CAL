<?php
    session_start();
    require 'config.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy();
    header('Location: login.php');
}
?>