<?php
session_start();
include("conexion.php");

// Verifica sesión de administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    echo "<script>alert('Acceso no autorizado.'); window.location.href = '../screens/login.html';</script>";
    exit();
}

$id_admin = intval($_SESSION['usuario_id']);

// Validar ID del curso a eliminar
if (!isset($_GET['id'])) {
    echo "<script>alert('ID de curso inválido.'); window.location.href = 'mis_cursos_admin.php';</script>";
    exit();
}

$id_curso = intval($_GET['id']);

// Verificar que el curso pertenece al admin actual
$query = "SELECT archivo FROM cursos WHERE id = $id_curso AND id_admin = $id_admin LIMIT 1";
$result = mysqli_query($conexion, $query);

if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Curso no encontrado o no tienes permiso para eliminarlo.'); window.location.href = 'mis_cursos_admin.php';</script>";
    exit();
}

$curso = mysqli_fetch_assoc($result);
$archivo = $curso['archivo'];

// Eliminar el curso de la base de datos
$delete_query = "DELETE FROM cursos WHERE id = $id_curso AND id_admin = $id_admin";
if (mysqli_query($conexion, $delete_query)) {
    // Eliminar el archivo del servidor
    $ruta_archivo = "../uploads/" . $archivo;
    if (file_exists($ruta_archivo)) {
        unlink($ruta_archivo);
    }

    echo "<script>alert('Curso eliminado correctamente.'); window.location.href = 'mis_cursos_admin.php';</script>";
} else {
    echo "<script>alert('Error al eliminar el curso.'); window.location.href = 'mis_cursos_admin.php';</script>";
}
?>
