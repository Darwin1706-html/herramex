<?php
require_once __DIR__ . '/funciones.php';
$cartCount = cart_count();
$currentUser = current_user();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HERRAMEX | Herramientas que construyen tu futuro</title>
    <meta name="description" content="HERRAMEX, ferretería especializada en herramientas, materiales de construcción, productos de jardinería y seguridad industrial.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a href="index.php" class="brand" aria-label="HERRAMEX inicio">
            <span class="brand-mark">H</span>
            <span>
                <strong>HERRAMEX</strong>
                <small>Ferretería profesional</small>
            </span>
        </a>

        <nav class="main-nav" aria-label="Navegación principal">
            <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Inicio</a>
            <a href="catalogo.php" class="<?php echo $currentPage === 'catalogo.php' ? 'active' : ''; ?>">Catálogo</a>
            <a href="nosotros.php" class="<?php echo $currentPage === 'nosotros.php' ? 'active' : ''; ?>">Nosotros</a>
            <a href="contacto.php" class="<?php echo $currentPage === 'contacto.php' ? 'active' : ''; ?>">Contacto</a>
        </nav>

        <div class="header-tools">
            <form class="search-bar" method="get" action="catalogo.php">
                <input type="search" name="q" placeholder="Buscar productos" value="<?php echo e($_GET['q'] ?? ''); ?>" aria-label="Buscar productos">
                <button type="submit">Buscar</button>
            </form>

            <?php if ($currentUser): ?>
                <div class="user-pill">
                    <span>Hola, <?php echo e($currentUser['nombre']); ?></span>
                    <?php if ($currentUser['rol'] === 'admin' || $currentUser['rol'] === 'empleado'): ?>
                        <a href="admin/index.php" class="btn btn-link">Admin</a>
                    <?php endif; ?>
                    <a href="logout.php" class="btn btn-link">Salir</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary">Iniciar sesión</a>
            <?php endif; ?>

            <a href="carrito.php" class="cart-link" aria-label="Carrito de compras">
                <span>🛒</span>
                <span class="cart-count"><?php echo $cartCount; ?></span>
            </a>
        </div>
    </div>
</header>
<main>
    <div class="container">
        <?php render_messages(); ?>
    </div>
