<?php 
session_start();
include("conexion.php");

if (!isset($_POST['correo'], $_POST['contrasena'])) {
    echo "<script>alert('Faltan datos.'); window.location.href = '../screens/index.html';</script>";
    exit();
}

$correo = mysqli_real_escape_string($conexion, $_POST['correo']);
$contrasena = $_POST['contrasena'];

// Obtener al usuario (sin necesidad de que el rol sea enviado)
$query = "SELECT * FROM usuarios WHERE correo = '$correo'";
$resultado = mysqli_query($conexion, $query);

if (!$resultado || mysqli_num_rows($resultado) === 0) {
    echo "<script>alert('Usuario no encontrado.'); window.location.href = '../screens/index.html';</script>";
    exit();
}

$usuario = mysqli_fetch_assoc($resultado);

// Verificar contraseña
if (password_verify($contrasena, $usuario['contrasena'])) {
    $_SESSION['usuario'] = $correo;
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['rol'] = $usuario['rol'];

    if ($usuario['rol'] === 'admin') {
        header("Location: ../screens/subircursos.html");
    } else {
        header("Location: ../screens/principal.html");
    }
    exit();
} else {
    echo "<script>alert('Contraseña incorrecta.'); window.location.href = '../screens/index.html';</script>";
    exit();
}
?>
