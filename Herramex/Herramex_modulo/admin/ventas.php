<?php
require_once __DIR__ . '/header.php';

$pdo = getDb();
$ventas = $pdo->query('SELECT v.*, u.nombre AS usuario, c.nombre AS cliente, m.nombre AS metodo FROM ventas v LEFT JOIN usuarios u ON u.id = v.usuario_id LEFT JOIN clientes c ON c.id = v.cliente_id LEFT JOIN metodos_pago m ON m.id = v.metodo_pago_id ORDER BY v.fecha_venta DESC')->fetchAll();
?>

<div class="admin-panel">
    <div class="action-bar">
        <h2>Ventas</h2>
    </div>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Usuario</th>
                    <th>Método</th>
                    <th>Total</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?php echo (int) $venta['id']; ?></td>
                        <td><?php echo e($venta['cliente'] ?? '-'); ?></td>
                        <td><?php echo e($venta['usuario'] ?? '-'); ?></td>
                        <td><?php echo e($venta['metodo'] ?? '-'); ?></td>
                        <td><?php echo format_money($venta['total']); ?></td>
                        <td><?php echo e($venta['fecha_venta']); ?></td>
                        <td><?php echo e($venta['estado']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
