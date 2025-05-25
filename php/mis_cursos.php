<?php
require_once 'funciones.php';

if (!estaLogueado()) {
    header('Location: ../login.php');
    exit;
}

$usuario_id = obtenerUsuarioId();
$cursos = obtenerCursosComprados($usuario_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mis Cursos - Cursita</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/styles.css" />
</head>
<body>
  <header class="bg-primary text-white text-center py-4 shadow">
    <h1 class="fw-bold">Cursita</h1>
    <nav class="d-flex justify-content-center gap-3 mt-3">
      <a href="../index.html" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">Inicio</a>
      <a href="../index.html#cursos" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">Cursos</a>
      <a href="../nosotros.html" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">Nosotros</a>
      <a href="#carrito" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">Carrito</a>
    </nav>
  </header>

  <section class="container my-5">
    <h2 class="text-center text-primary fw-bold mb-4">Mis Cursos Comprados</h2>
    
    <?php if (empty($cursos)): ?>
      <div class="text-center py-5">
        <h5 class="text-muted">No has comprado ningún curso aún</h5>
        <a href="../index.html#cursos" class="btn btn-primary mt-3">Explorar cursos</a>
      </div>
    <?php else: ?>
      <div class="row g-4">
        <?php foreach ($cursos as $curso): ?>
          <div class="col-md-4">
            <div class="card shadow border-0 h-100">
              <div class="card-body text-center">
                <h3 class="card-title fw-bold text-primary"><?= htmlspecialchars($curso['nombre_curso']) ?></h3>
                <p class="text-success fw-bold">$<?= number_format($curso['precio'], 2) ?></p>
                <p class="text-muted small">Comprado el: <?= date('d/m/Y', strtotime($curso['fecha'])) ?></p>
                <button class="btn btn-primary rounded-pill px-4">Acceder al curso</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <footer class="bg-primary text-white text-center p-3">
    <p class="mb-0">&copy; 2025 Cursita - Aprende a tu ritmo</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>