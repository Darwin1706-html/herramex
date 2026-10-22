<?php require_once __DIR__ . '/header.php'; ?>

<section class="stat-grid">
    <div class="stat-card">
        <small>Total de productos</small>
        <strong><?php echo number_format($dashboardStats['productos']); ?></strong>
    </div>
    <div class="stat-card">
        <small>Existencias bajas</small>
        <strong><?php echo number_format($dashboardStats['bajos']); ?></strong>
    </div>
    <div class="stat-card">
        <small>Clientes registrados</small>
        <strong><?php echo number_format($dashboardStats['clientes']); ?></strong>
    </div>
    <div class="stat-card">
        <small>Ventas realizadas</small>
        <strong><?php echo number_format($dashboardStats['ventas']); ?></strong>
    </div>
    <div class="stat-card">
        <small>Ingresos</small>
        <strong><?php echo format_money($dashboardStats['ingresos']); ?></strong>
    </div>
</section>

<div class="admin-panel">
    <h2>Resumen del negocio</h2>
    <p class="muted">El panel centraliza indicadores clave del inventario, clientes y ventas.</p>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
