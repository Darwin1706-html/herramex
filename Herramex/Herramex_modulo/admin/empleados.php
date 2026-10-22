<?php
require_once __DIR__ . '/header.php';

$pdo = getDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $puesto = trim($_POST['puesto'] ?? '');
    $estado = $_POST['estado'] ?? 'activo';

    if ($nombre === '' || $apellido === '' || $email === '') {
        set_message('danger', 'Nombre, apellido y correo son obligatorios.');
        redirect('empleados.php');
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE empleados SET nombre = :nombre, apellido = :apellido, email = :email, telefono = :telefono, puesto = :puesto, estado = :estado WHERE id = :id');
        $stmt->execute([':nombre' => $nombre, ':apellido' => $apellido, ':email' => $email, ':telefono' => $telefono, ':puesto' => $puesto, ':estado' => $estado, ':id' => $id]);
        set_message('success', 'Empleado actualizado.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO empleados (nombre, apellido, email, telefono, puesto, estado) VALUES (:nombre, :apellido, :email, :telefono, :puesto, :estado)');
        $stmt->execute([':nombre' => $nombre, ':apellido' => $apellido, ':email' => $email, ':telefono' => $telefono, ':puesto' => $puesto, ':estado' => $estado]);
        set_message('success', 'Empleado guardado.');
    }

    redirect('empleados.php');
}

if (isset($_GET['delete']) && (int) $_GET['delete'] > 0) {
    $pdo->prepare('DELETE FROM empleados WHERE id = :id')->execute([':id' => (int) $_GET['delete']]);
    set_message('success', 'Empleado eliminado.');
    redirect('empleados.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editEmpleado = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM empleados WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editEmpleado = $stmt->fetch();
}

$empleados = $pdo->query('SELECT * FROM empleados ORDER BY created_at DESC')->fetchAll();
?>

<div class="admin-panel">
    <div class="action-bar">
        <h2>Empleados</h2>
        <a href="empleados.php" class="btn btn-dark">Nuevo empleado</a>
    </div>
    <div class="form-shell">
        <form method="post" class="form-card">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int) ($editEmpleado['id'] ?? 0); ?>">
            <div class="form-grid">
                <div class="form-row row-2">
                    <div>
                        <label for="nombre">Nombre</label>
                        <input id="nombre" name="nombre" type="text" value="<?php echo e($editEmpleado['nombre'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="apellido">Apellido</label>
                        <input id="apellido" name="apellido" type="text" value="<?php echo e($editEmpleado['apellido'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <label for="email">Correo</label>
                    <input id="email" name="email" type="email" value="<?php echo e($editEmpleado['email'] ?? ''); ?>" required>
                </div>
                <div class="form-row row-2">
                    <div>
                        <label for="telefono">Teléfono</label>
                        <input id="telefono" name="telefono" type="text" value="<?php echo e($editEmpleado['telefono'] ?? ''); ?>">
                    </div>
                    <div>
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="activo" <?php echo (($editEmpleado['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?php echo (($editEmpleado['estado'] ?? 'activo') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label for="puesto">Puesto</label>
                    <input id="puesto" name="puesto" type="text" value="<?php echo e($editEmpleado['puesto'] ?? ''); ?>">
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $editEmpleado ? 'Actualizar' : 'Guardar'; ?></button>
            </div>
        </form>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Puesto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($empleados as $empleado): ?>
                        <tr>
                            <td><?php echo (int) $empleado['id']; ?></td>
                            <td><?php echo e($empleado['nombre'] . ' ' . $empleado['apellido']); ?></td>
                            <td><?php echo e($empleado['email']); ?></td>
                            <td><?php echo e($empleado['puesto'] ?? '-'); ?></td>
                            <td><?php echo e($empleado['estado']); ?></td>
                            <td>
                                <a href="empleados.php?edit=<?php echo (int) $empleado['id']; ?>" class="btn btn-outline">Editar</a>
                                <a href="empleados.php?delete=<?php echo (int) $empleado['id']; ?>" class="btn btn-secondary" onclick="return confirm('¿Eliminar empleado?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
