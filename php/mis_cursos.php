<?php
require_once 'funciones.php';

if (!estaLogueado()) {
    header('Location: ../screens/index.html');
    exit;
}

$usuario_id = obtenerUsuarioId();
$cursos = obtenerCursosComprados($usuario_id);

// Incluir la vista
require '../screens/miscursos.html';
?>