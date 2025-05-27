<?php
session_start();
include("conexion.php");

// Verifica que el usuario esté logueado y sea admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    echo "<script>alert('Acceso no autorizado.'); window.location.href = '../screens/login.html';</script>";
    exit();
}

// Verifica que los datos estén completos
if (!isset($_POST['titulo'], $_POST['descripcion']) || $_FILES['archivo_curso']['error'] !== 0) {
    echo "<script>alert('Faltan datos o archivo inválido.'); window.location.href = '../screens/subircursos.html';</script>";
    exit();
}

$titulo = mysqli_real_escape_string($conexion, $_POST['titulo']);
$descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
$id_admin = $_SESSION['usuario_id'];

// Manejo del archivo
$archivo_nombre = $_FILES['archivo_curso']['name'];
$archivo_temp = $_FILES['archivo_curso']['tmp_name'];
$ruta_destino = "../uploads/" . basename($archivo_nombre);

// Asegura que la carpeta exista
if (!is_dir("../uploads")) {
    mkdir("../uploads", 0777, true);
}

if (move_uploaded_file($archivo_temp, $ruta_destino)) {
    // Inserta en la base de datos
    $query = "INSERT INTO cursos (titulo, descripcion, archivo, id_admin) 
              VALUES ('$titulo', '$descripcion', '$archivo_nombre', '$id_admin')";

    if (mysqli_query($conexion, $query)) {
        echo "<script>alert('Curso subido con éxito.'); window.location.href = '../php/mis_cursos_admin.php';</script>";
    } else {
        echo "<script>alert('Error al guardar en la base de datos.'); window.location.href = '../screens/subircursos.html';</script>";
    }
} else {
    echo "<script>alert('Error al subir el archivo.'); window.location.href = '../screens/subircursos.html';</script>";
}
?>
