<?php
session_start();
include("conexion.php");

if (!isset($_POST['correo']) || !isset($_POST['contrasena'])) {
    echo "<script>alert('Faltan datos.'); window.location.href = '../screens/login.html';</script>";
    exit();
}

$correo = mysqli_real_escape_string($conexion, $_POST['correo']);
$contrasena = $_POST['contrasena'];

$query = "SELECT * FROM usuarios WHERE correo = '$correo'";
$resultado = mysqli_query($conexion, $query);

if (!$resultado) {
    echo "<script>alert('Error en la consulta.'); window.location.href = '../screens/login.html';</script>";
    exit();
}

if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    
  if (password_verify($contrasena, $usuario['contrasena'])) {
    $_SESSION['usuario'] = $correo;
    $_SESSION['usuario_id'] = $usuario['id']; // ✅ Añade esto
    header("Location: ../screens/index.html");
    exit();
} else {
        echo "<script>alert('Usuario o contraseña incorrectos.'); window.location.href = '../screens/login.html';</script>";
        exit();
    }
} else {
    echo "<script>alert('Usuario o contraseña incorrectos.'); window.location.href = '../screens/login.html';</script>";
    exit();
}
?>
