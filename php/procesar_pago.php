<?php
// Iniciar buffer de salida para capturar cualquier output no deseado
ob_start();

require_once 'funciones.php';

// Configurar headers para respuesta JSON
header('Content-Type: application/json; charset=utf-8');

// Iniciar sesión y verificar autenticación
session_start() or die(json_encode(['success' => false, 'message' => 'No se pudo iniciar sesión']));

try {
    // Verificar si el usuario está logueado
    if (!estaLogueado()) {
        throw new Exception('Debes iniciar sesión para completar la compra', 401);
    }

    // Obtener y validar datos JSON
    $jsonInput = file_get_contents('php://input');
    if ($jsonInput === false) {
        throw new Exception('Error al leer los datos de entrada', 400);
    }

    $input = json_decode($jsonInput, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Datos JSON inválidos: ' . json_last_error_msg(), 400);
    }

    // Validar datos requeridos
    $metodo_pago = $input['metodo_pago'] ?? '';
    $carrito = $input['carrito'] ?? [];

    if (empty($metodo_pago)) {
        throw new Exception('Método de pago no especificado', 400);
    }

    if (empty($carrito) || !is_array($carrito)) {
        throw new Exception('Carrito de compras vacío o inválido', 400);
    }

    // Calcular total y validar precios
    $total = 0;
    foreach ($carrito as $curso) {
        if (!isset($curso['precio']) || !is_numeric($curso['precio'])) {
            throw new Exception('Precio inválido en uno de los cursos', 400);
        }
        $total += (float)$curso['precio'];
    }

    // Obtener ID de usuario
    $usuario_id = obtenerUsuarioId();
    if (!$usuario_id) {
        throw new Exception('No se pudo identificar al usuario', 500);
    }

    // Registrar la compra
    if (!registrarCompra($usuario_id, $carrito, $metodo_pago, $total)) {
        throw new Exception('Error al registrar la compra en la base de datos', 500);
    }

    // Limpiar buffer y enviar respuesta exitosa
    ob_end_clean();
    echo json_encode([
        'success' => true,
        'message' => 'Procesando compra de: ',
        'total' => $total
    ]);

} catch (Exception $e) {
    // Limpiar buffer y enviar error
    ob_end_clean();
    http_response_code($e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
}

exit; // Asegurar que no se envíe nada más después
?>