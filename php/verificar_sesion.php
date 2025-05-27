<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'loggedin' => isset($_SESSION['usuario']),
    'usuario' => $_SESSION['usuario'] ?? null
]);
?>