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
        set_message('danger', 'El nombre de la categoría es obligatorio.');
        redirect('categorias.php');
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE categorias SET nombre = :nombre, descripcion = :descripcion, estado = :estado WHERE id = :id');
        $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion, ':estado' => $estado, ':id' => $id]);
        set_message('success', 'Categoría actualizada.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO categorias (nombre, descripcion, estado) VALUES (:nombre, :descripcion, :estado)');
        $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion, ':estado' => $estado]);
        set_message('success', 'Categoría creada.');
    }

    redirect('categorias.php');
}

if (isset($_GET['delete']) && (int) $_GET['delete'] > 0) {
    $id = (int) $_GET['delete'];
    $pdo->prepare('DELETE FROM categorias WHERE id = :id')->execute([':id' => $id]);
    set_message('success', 'Categoría eliminada.');
    redirect('categorias.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editCategoria = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editCategoria = $stmt->fetch();
}

$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nombre ASC')->fetchAll();
?>

<div class="admin-panel">
    <div class="action-bar">
        <h2>Gestión de categorías</h2>
        <a href="categorias.php" class="btn btn-dark">Nueva categoría</a>
    </div>

    <div class="form-shell">
        <form method="post" class="form-card">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int) ($editCategoria['id'] ?? 0); ?>">
            <div class="form-grid">
                <div class="form-row">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" type="text" value="<?php echo e($editCategoria['nombre'] ?? ''); ?>" required>
                </div>
                <div class="form-row">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4"><?php echo e($editCategoria['descripcion'] ?? ''); ?></textarea>
                </div>
                <div class="form-row">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado">
                        <option value="activo" <?php echo (($editCategoria['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo (($editCategoria['estado'] ?? 'activo') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $editCategoria ? 'Actualizar' : 'Guardar'; ?></button>
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
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>
                            <td><?php echo (int) $categoria['id']; ?></td>
                            <td><?php echo e($categoria['nombre']); ?></td>
                            <td><?php echo e($categoria['descripcion'] ?? '-'); ?></td>
                            <td><?php echo e($categoria['estado']); ?></td>
                            <td>
                                <a href="categorias.php?edit=<?php echo (int) $categoria['id']; ?>" class="btn btn-outline">Editar</a>
                                <a href="categorias.php?delete=<?php echo (int) $categoria['id']; ?>" class="btn btn-secondary" onclick="return confirm('¿Eliminar categoría?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
