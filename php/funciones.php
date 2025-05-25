<?php
session_start();

function conectarDB() {
    $host = "localhost";
    $dbname = "cursita";
    $username = "root";
    $password = "";
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function obtenerUsuarioId() {
    return $_SESSION['usuario_id'] ?? null;
}

function registrarCompra($usuario_id, $cursos, $metodo_pago, $total) {
    $conn = conectarDB();
    
    try {
        $conn->beginTransaction();
        
        // Insertar la compra
        $stmt = $conn->prepare("INSERT INTO compras (usuario_id, fecha, metodo_pago, total) VALUES (?, NOW(), ?, ?)");
        $stmt->execute([$usuario_id, $metodo_pago, $total]);
        $compra_id = $conn->lastInsertId();
        
        // Insertar los cursos comprados
        foreach ($cursos as $curso) {
            $stmt = $conn->prepare("INSERT INTO cursos_comprados (compra_id, nombre_curso, precio) VALUES (?, ?, ?)");
            $stmt->execute([$compra_id, $curso['nombre'], $curso['precio']]);
        }
        
        $conn->commit();
        return true;
    } catch(PDOException $e) {
        $conn->rollBack();
        return false;
    }
}

function obtenerCursosComprados($usuario_id) {
    $conn = conectarDB();
    
    $stmt = $conn->prepare("
        SELECT c.nombre_curso, c.precio, co.fecha 
        FROM cursos_comprados c
        JOIN compras co ON c.compra_id = co.id
        WHERE co.usuario_id = ?
        ORDER BY co.fecha DESC
    ");
    $stmt->execute([$usuario_id]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>