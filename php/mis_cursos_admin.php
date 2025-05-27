<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include("conexion.php");

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verifica que sea un admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    echo "<script>alert('Acceso no autorizado.'); window.location.href = '../screens/login.html';</script>";
    exit();
}

$id_admin = intval($_SESSION['usuario_id']);

// Traer los cursos que subió este admin
$query = "SELECT * FROM cursos WHERE id_admin = $id_admin ORDER BY id DESC";
$result = mysqli_query($conexion, $query);

if (!$result) {
    die("Error al ejecutar la consulta: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Mis Cursos Subidos</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f8f9fa;
      margin: 0;
      padding: 20px;
    }
    h1 {
      color: #2c3e50;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background: white;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 12px;
      border: 1px solid #ddd;
    }
    th {
      background-color: #34495e;
      color: white;
    }
    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
    a {
      color: #2980b9;
      text-decoration: none;
    }
    .btn {
      background-color: #e74c3c;
      color: white;
      padding: 5px 10px;
      text-decoration: none;
      border-radius: 5px;
    }
    .btn:hover {
      background-color: #c0392b;
    }
  </style>
</head>
<body>

  <h1>Mis Cursos Subidos</h1>

  <?php if (mysqli_num_rows($result) > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Título</th>
          <th>Descripción</th>
          <th>Archivo</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php while($curso = mysqli_fetch_assoc($result)): ?>
          <tr>
            <td><?= htmlspecialchars($curso['titulo']) ?></td>
            <td><?= htmlspecialchars($curso['descripcion']) ?></td>
            <td><a href="../uploads/<?= urlencode($curso['archivo']) ?>" target="_blank">Ver archivo</a></td>
            <td><a class="btn" href="eliminar_curso.php?id=<?= $curso['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este curso?')">Eliminar</a></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No has subido ningún curso aún.</p>
  <?php endif; ?>

  <p><a href="../screens/subircursos.html">← Subir otro curso</a></p>
  <p><a href="../screens/subircursos.html">Volver al inicio</a></p>
</body>
</html>
