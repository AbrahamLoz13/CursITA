<?php
$host = "localhost";
$user = "root";
$password = ""; // por defecto en XAMPP
$db = "cursita"; // asegúrate que es tu nombre real

$conexion = mysqli_connect($host, $user, $password, $db);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}
?>
