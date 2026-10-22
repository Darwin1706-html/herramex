<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/funciones.php';

require_admin();
$currentPage = basename($_SERVER['PHP_SELF']);
$dashboardStats = get_dashboard_stats();
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración HERRAMEX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">
<div class="admin-layout">
    <aside class="sidebar">
        <h3>HERRAMEX</h3>
        <nav>
            <a href="index.php" class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Dashboard</a>
            <a href="productos.php" class="<?php echo $currentPage === 'productos.php' ? 'active' : ''; ?>">Productos</a>
            <a href="categorias.php" class="<?php echo $currentPage === 'categorias.php' ? 'active' : ''; ?>">Categorías</a>
            <a href="clientes.php" class="<?php echo $currentPage === 'clientes.php' ? 'active' : ''; ?>">Clientes</a>
            <a href="empleados.php" class="<?php echo $currentPage === 'empleados.php' ? 'active' : ''; ?>">Empleados</a>
            <a href="proveedores.php" class="<?php echo $currentPage === 'proveedores.php' ? 'active' : ''; ?>">Proveedores</a>
            <a href="ventas.php" class="<?php echo $currentPage === 'ventas.php' ? 'active' : ''; ?>">Ventas</a>
            <a href="metodos_pago.php" class="<?php echo $currentPage === 'metodos_pago.php' ? 'active' : ''; ?>">Métodos de pago</a>
            <a href="acerca.php" class="<?php echo $currentPage === 'acerca.php' ? 'active' : ''; ?>">Acerca de</a>
            <a href="../index.php">Ir a la tienda</a>
            <a href="../logout.php">Cerrar sesión</a>
        </nav>
    </aside>

    <main class="admin-main">
        <div class="admin-topbar">
            <h1>Panel administrativo</h1>
            <span class="muted"><?php echo e(current_user()['nombre'] ?? 'Administrador'); ?></span>
        </div>
        <?php render_messages(); ?>
