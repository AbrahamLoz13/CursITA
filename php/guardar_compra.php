<?php
header('Content-Type: application/json');

// Incluir conexión
require_once 'conexion.php';

// Leer datos JSON de POST
$datos = json_decode(file_get_contents('php://input'), true);

if (!$datos || !isset($datos['cursos']) || !isset($datos['total']) || !isset($datos['metodoPago'])) {
  echo json_encode(['exito' => false, 'error' => 'Datos incompletos']);
  exit;
}

$cursos = $datos['cursos'];
$total = floatval($datos['total']);
$metodoPago = $datos['metodoPago'];

// Sanitizar nombre titular
$nombreTitular = $conn->real_escape_string($metodoPago['nombreTitular']);
$fechaCompra = date('Y-m-d H:i:s');

$conn->begin_transaction();

try {
  // Insertar compra
  $sqlCompra = "INSERT INTO compras (nombre_titular, total, fecha_compra) VALUES ('$nombreTitular', $total, '$fechaCompra')";
  if (!$conn->query($sqlCompra)) throw new Exception('Error al guardar compra.');

  $idCompra = $conn->insert_id;

  // Preparar insert para cursos comprados
  $stmtCurso = $conn->prepare("INSERT INTO cursos_comprados (id_compra, nombre, precio, img, fecha_compra) VALUES (?, ?, ?, ?, ?)");
  if (!$stmtCurso) throw new Exception('Error en preparación de consulta.');

  foreach ($cursos as $curso) {
    $nombre = $conn->real_escape_string($curso['nombre']);
    $precio = floatval($curso['precio']);
    $img = $conn->real_escape_string($curso['img']);
    $stmtCurso->bind_param("isdss", $idCompra, $nombre, $precio, $img, $fechaCompra);
    if (!$stmtCurso->execute()) throw new Exception('Error al guardar curso comprado.');
  }
  $conn->commit();

  echo json_encode(['exito' => true]);
} catch (Exception $e) {
  $conn->rollback();
  echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>
