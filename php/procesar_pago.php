<?php
require_once 'funciones.php';

header('Content-Type: application/json');

if (!estaLogueado()) {
    echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para completar la compra']);
    exit;
}

$metodo_pago = $_POST['metodo_pago'] ?? '';
$carrito = json_decode($_POST['carrito'], true);

if (empty($metodo_pago) || empty($carrito)) {
    echo json_encode(['success' => false, 'message' => 'Datos de compra incompletos']);
    exit;
}

// Calcular total
$total = array_reduce($carrito, function($sum, $curso) {
    return $sum + $curso['precio'];
}, 0);

$usuario_id = obtenerUsuarioId();

if (registrarCompra($usuario_id, $carrito, $metodo_pago, $total)) {
    echo json_encode(['success' => true, 'message' => 'Compra realizada con éxito']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al registrar la compra']);
}
?>