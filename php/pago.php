<?php
session_start();
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "Tu carrito está vacío.";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pago - Cursita</title>
</head>
<body>
    <h2>Información de Pago</h2>
    <form action="procesar_pago.php" method="post">
        <label>Nombre del titular:</label><br>
        <input type="text" name="nombre_titular" required><br>

        <label>Número de tarjeta:</label><br>
        <input type="text" name="numero_tarjeta" required><br>

        <label>Método de pago:</label><br>
        <select name="metodo_pago" required>
            <option value="tarjeta">Tarjeta</option>
            <option value="paypal">PayPal</option>
        </select><br><br>

        <button type="submit">Confirmar compra</button>
    </form>
</body>
</html>
