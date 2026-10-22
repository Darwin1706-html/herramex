<?php
require_once __DIR__ . '/header.php';

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $estado = $_POST['estado'] ?? 'activo';

    if ($nombre === '') {
        set_message('danger', 'El nombre del método de pago es obligatorio.');
        redirect('metodos_pago.php');
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE metodos_pago SET nombre = :nombre, descripcion = :descripcion, estado = :estado WHERE id = :id');
        $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion, ':estado' => $estado, ':id' => $id]);
        set_message('success', 'Método actualizado.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO metodos_pago (nombre, descripcion, estado) VALUES (:nombre, :descripcion, :estado)');
        $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion, ':estado' => $estado]);
        set_message('success', 'Método guardado.');
    }

    redirect('metodos_pago.php');
}

if (isset($_GET['delete']) && (int) $_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM metodos_pago WHERE id = :id')->execute([':id' => (int) $_GET['delete']]);
    set_message('success', 'Método eliminado.');
    redirect('metodos_pago.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editMethod = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM metodos_pago WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editMethod = $stmt->fetch();
}

$metodos = $pdo->query('SELECT * FROM metodos_pago ORDER BY nombre ASC')->fetchAll();
?>

<div class="admin-panel">
    <div class="action-bar">
        <h2>Métodos de pago</h2>
        <a href="metodos_pago.php" class="btn btn-dark">Nuevo método</a>
    </div>
    <div class="form-shell">
        <form method="post" class="form-card">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int) ($editMethod['id'] ?? 0); ?>">
            <div class="form-grid">
                <div class="form-row">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" type="text" value="<?php echo e($editMethod['nombre'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?php echo e($editMethod['descripcion'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" <?php echo (($editMethod['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo (($editMethod['estado'] ?? 'activo') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $editMethod ? 'Actualizar' : 'Guardar'; ?></button>
            </div>
        </form>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($metodos as $metodo): ?>
                        <tr>
                            <td><?php echo (int) $metodo['id']; ?></td>
                            <td><?php echo e($metodo['nombre']); ?></td>
                            <td><?php echo e($metodo['descripcion'] ?? '-'); ?></td>
                            <td><?php echo e($metodo['estado']); ?></td>
                            <td>
                                <a href="metodos_pago.php?edit=<?php echo (int) $metodo['id']; ?>" class="btn btn-outline">Editar</a>
                                <a href="metodos_pago.php?delete=<?php echo (int) $metodo['id']; ?>" class="btn btn-secondary" onclick="return confirm('¿Eliminar método?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
