<?php
session_start();
include("conexion.php");

if (!isset($_POST['correo'], $_POST['contrasena'], $_POST['contrasena_confirm'])) {
    echo "<script>alert('Faltan datos.'); window.location.href = '../screens/register.html';</script>";
    exit();
}

$correo = mysqli_real_escape_string($conexion, $_POST['correo']);
$contrasena = $_POST['contrasena'];
$contrasena_confirm = $_POST['contrasena_confirm'];

if ($contrasena !== $contrasena_confirm) {
    echo "<script>alert('Las contraseñas no coinciden.'); window.location.href = '../screens/register.html';</script>";
    exit();
}

// Validar que el correo no exista
$query = "SELECT * FROM usuarios WHERE correo = '$correo'";
$result = mysqli_query($conexion, $query);
if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('El correo ya está registrado.'); window.location.href = '../screens/register.html';</script>";
    exit();
}

// Hashear contraseña para mayor seguridad
$contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

$query_insert = "INSERT INTO usuarios (correo, contrasena) VALUES ('$correo', '$contrasena_hash')";
if (mysqli_query($conexion, $query_insert)) {
    echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href = '../screens/login.html';</script>";
    exit();
} else {
    echo "<script>alert('Error al registrar usuario.'); window.location.href = '../screens/register.html';</script>";
    exit();
}
?>
