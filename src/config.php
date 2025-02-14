<?php

$pdo = new PDO('mysql:host=localhost;dbname=reserv_call', 'root', 'azerty/123', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);