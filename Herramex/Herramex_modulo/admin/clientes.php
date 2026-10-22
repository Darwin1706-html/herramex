<?php
require_once __DIR__ . '/header.php';

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $estado = $_POST['estado'] ?? 'activo';

    if ($nombre === '' || $email === '') {
        set_message('danger', 'Nombre y correo son obligatorios.');
        redirect('clientes.php');
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE clientes SET nombre = :nombre, email = :email, telefono = :telefono, direccion = :direccion, estado = :estado WHERE id = :id');
        $stmt->execute([':nombre' => $nombre, ':email' => $email, ':telefono' => $telefono, ':direccion' => $direccion, ':estado' => $estado, ':id' => $id]);
        set_message('success', 'Cliente actualizado.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO clientes (nombre, email, telefono, direccion, estado) VALUES (:nombre, :email, :telefono, :direccion, :estado)');
        $stmt->execute([':nombre' => $nombre, ':email' => $email, ':telefono' => $telefono, ':direccion' => $direccion, ':estado' => $estado]);
        set_message('success', 'Cliente registrado.');
    }

    redirect('clientes.php');
}

if (isset($_GET['delete']) && (int) $_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM clientes WHERE id = :id')->execute([':id' => (int) $_GET['delete']]);
    set_message('success', 'Cliente eliminado.');
    redirect('clientes.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editClient = null;
if ($editId > 0) {
    $editStmt = $pdo->prepare('SELECT * FROM clientes WHERE id = :id LIMIT 1');
    $editStmt->execute([':id' => $editId]);
    $editClient = $editStmt->fetch();
}

$clientes = $pdo->query('SELECT * FROM clientes ORDER BY created_at DESC')->fetchAll();
?>

<div class="admin-panel">
    <div class="action-bar">
        <h2>Clientes</h2>
        <a href="clientes.php" class="btn btn-dark">Nuevo cliente</a>
    </div>
    <div class="form-shell">
        <form method="post" class="form-card">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int) ($editClient['id'] ?? 0); ?>">
            <div class="form-grid">
                <div class="form-row">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" type="text" value="<?php echo e($editClient['nombre'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <label for="email">Correo</label>
                    <input id="email" name="email" type="email" value="<?php echo e($editClient['email'] ?? ''); ?>" required>
                </div>
                <div class="form-row row-2">
                    <div>
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" type="text" value="<?php echo e($editClient['telefono'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="activo" <?php echo (($editClient['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?php echo (($editClient['estado'] ?? 'activo') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" rows="3"><?php echo e($editClient['direccion'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $editClient ? 'Actualizar' : 'Guardar'; ?></button>
            </div>
        </form>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo (int) $cliente['id']; ?></td>
                            <td><?php echo e($cliente['nombre']); ?></td>
                            <td><?php echo e($cliente['email']); ?></td>
                            <td><?php echo e($cliente['telefono'] ?? '-'); ?></td>
                            <td><?php echo e($cliente['estado']); ?></td>
                            <td>
                                <a href="clientes.php?edit=<?php echo (int) $cliente['id']; ?>" class="btn btn-outline">Editar</a>
                                <a href="clientes.php?delete=<?php echo (int) $cliente['id']; ?>" class="btn btn-secondary" onclick="return confirm('¿Eliminar cliente?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
