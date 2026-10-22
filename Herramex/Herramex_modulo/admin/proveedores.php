<?php
require_once __DIR__ . '/header.php';

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nombre = trim($_POST['nombre'] ?? '');
    $contacto = trim($_POST['contacto'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $estado = $_POST['estado'] ?? 'activo';

    if ($nombre === '') {
        set_message('danger', 'El nombre del proveedor es obligatorio.');
        redirect('proveedores.php');
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE proveedores SET nombre = :nombre, contacto = :contacto, email = :email, telefono = :telefono, direccion = :direccion, estado = :estado WHERE id = :id');
        $stmt->execute([':nombre' => $nombre, ':contacto' => $contacto, ':email' => $email, ':telefono' => $telefono, ':direccion' => $direccion, ':estado' => $estado, ':id' => $id]);
        set_message('success', 'Proveedor actualizado.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO proveedores (nombre, contacto, email, telefono, direccion, estado) VALUES (:nombre, :contacto, :email, :telefono, :direccion, :estado)');
        $stmt->execute([':nombre' => $nombre, ':contacto' => $contacto, ':email' => $email, ':telefono' => $telefono, ':direccion' => $direccion, ':estado' => $estado]);
        set_message('success', 'Proveedor guardado.');
    }

    redirect('proveedores.php');
}

if (isset($_GET['delete']) && (int) $_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM proveedores WHERE id = :id')->execute([':id' => (int) $_GET['delete']]);
    set_message('success', 'Proveedor eliminado.');
    redirect('proveedores.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editProveedor = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM proveedores WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editProveedor = $stmt->fetch();
}

$proveedores = $pdo->query('SELECT * FROM proveedores ORDER BY nombre ASC')->fetchAll();
?>

<div class="admin-panel">
    <div class="action-bar">
        <h2>Proveedores</h2>
        <a href="proveedores.php" class="btn btn-dark">Nuevo proveedor</a>
    </div>
    <div class="form-shell">
        <form method="post" class="form-card">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int) ($editProveedor['id'] ?? 0); ?>">
            <div class="form-grid">
                <div class="form-row">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" type="text" value="<?php echo e($editProveedor['nombre'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <label for="contacto">Contacto</label>
                    <input id="contacto" name="contacto" type="text" value="<?php echo e($editProveedor['contacto'] ?? ''); ?>">
                </div>
                <div class="form-row row-2">
                    <div>
                        <label for="email">Correo</label>
                        <input id="email" name="email" type="email" value="<?php echo e($editProveedor['email'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" type="text" value="<?php echo e($editProveedor['telefono'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" rows="3"><?php echo e($editProveedor['direccion'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" <?php echo (($editProveedor['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo (($editProveedor['estado'] ?? 'activo') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $editProveedor ? 'Actualizar' : 'Guardar'; ?></button>
            </div>
        </form>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <tr>
                            <td><?php echo (int) $proveedor['id']; ?></td>
                            <td><?php echo e($proveedor['nombre']); ?></td>
                            <td><?php echo e($proveedor['contacto'] ?? '-'); ?></td>
                            <td><?php echo e($proveedor['email'] ?? '-'); ?></td>
                            <td><?php echo e($proveedor['estado']); ?></td>
                            <td>
                                <a href="proveedores.php?edit=<?php echo (int) $proveedor['id']; ?>" class="btn btn-outline">Editar</a>
                                <a href="proveedores.php?delete=<?php echo (int) $proveedor['id']; ?>" class="btn btn-secondary" onclick="return confirm('¿Eliminar proveedor?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
