<?php
require_once __DIR__ . '/header.php';

$pdo = getDb();
$categories = $pdo->query('SELECT * FROM categorias ORDER BY nombre ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $codigo = trim($_POST['codigo'] ?? '');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoriaId = (int) ($_POST['categoria_id'] ?? 0);
    $precio = (float) ($_POST['precio'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $estado = $_POST['estado'] ?? 'activo';
    $promo = isset($_POST['es_promo']) ? 1 : 0;
    $imagen = trim($_POST['imagen'] ?? '');

    if ($codigo === '' || $nombre === '' || $categoriaId <= 0 || $precio <= 0) {
        set_message('danger', 'Completa los campos básicos del producto.');
        redirect('productos.php');
    }

    if ($id > 0) {
        $stmt = $pdo->prepare('UPDATE productos SET codigo = :codigo, nombre = :nombre, descripcion = :descripcion, categoria_id = :categoria_id, precio = :precio, stock = :stock, imagen = :imagen, estado = :estado, es_promo = :es_promo WHERE id = :id');
        $stmt->execute([':codigo' => $codigo, ':nombre' => $nombre, ':descripcion' => $descripcion, ':categoria_id' => $categoriaId, ':precio' => $precio, ':stock' => $stock, ':imagen' => $imagen, ':estado' => $estado, ':es_promo' => $promo, ':id' => $id]);
        set_message('success', 'Producto actualizado.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio, stock, imagen, estado, es_promo) VALUES (:codigo, :nombre, :descripcion, :categoria_id, :precio, :stock, :imagen, :estado, :es_promo)');
        $stmt->execute([':codigo' => $codigo, ':nombre' => $nombre, ':descripcion' => $descripcion, ':categoria_id' => $categoriaId, ':precio' => $precio, ':stock' => $stock, ':imagen' => $imagen, ':estado' => $estado, ':es_promo' => $promo]);
        set_message('success', 'Producto creado.');
    }

    redirect('productos.php');
}

if (isset($_GET['delete']) && (int) $_GET['delete'] > 0) {
    $id = (int) $_GET['delete'];
    $pdo->prepare('DELETE FROM productos WHERE id = :id')->execute([':id' => $id]);
    set_message('success', 'Producto eliminado.');
    redirect('productos.php');
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editProduct = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $editId]);
    $editProduct = $stmt->fetch();
}

$productos = $pdo->query('SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id = p.categoria_id ORDER BY p.id DESC')->fetchAll();
?>

<div class="admin-panel">
    <div class="action-bar">
        <h2>Gestión de productos</h2>
        <a href="productos.php" class="btn btn-dark">Nuevo producto</a>
    </div>

    <div class="form-shell">
        <form method="post" class="form-card">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id" value="<?php echo (int) ($editProduct['id'] ?? 0); ?>">
            <div class="form-grid">
                <div class="form-row row-2">
                    <div>
                        <label for="codigo">Código</label>
                        <input id="codigo" name="codigo" type="text" value="<?php echo e($editProduct['codigo'] ?? ''); ?>" required>
                    </div>
                    <div>
                        <label for="nombre">Nombre</label>
                        <input id="nombre" name="nombre" type="text" value="<?php echo e($editProduct['nombre'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-row">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="4" required><?php echo e($editProduct['descripcion'] ?? ''); ?></textarea>
                </div>
                <div class="form-row row-2">
                    <div>
                        <label for="categoria_id">Categoría</label>
                        <select id="categoria_id" name="categoria_id" required>
                            <option value="">Seleccione</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo (int) $category['id']; ?>" <?php echo (($editProduct['categoria_id'] ?? 0) == $category['id']) ? 'selected' : ''; ?>><?php echo e($category['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="precio">Precio</label>
                        <input id="precio" name="precio" type="number" step="0.01" min="0" value="<?php echo e($editProduct['precio'] ?? 0); ?>" required>
                    </div>
                </div>
                <div class="form-row row-2">
                    <div>
                        <label for="stock">Stock</label>
                        <input id="stock" name="stock" type="number" min="0" value="<?php echo (int) ($editProduct['stock'] ?? 0); ?>">
                    </div>
                    <div>
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="activo" <?php echo (($editProduct['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                            <option value="inactivo" <?php echo (($editProduct['estado'] ?? 'activo') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label for="imagen">URL de la imagen</label>
                    <input id="imagen" name="imagen" type="text" value="<?php echo e($editProduct['imagen'] ?? ''); ?>">
                </div>
                <div class="form-row">
                    <label>
                        <input type="checkbox" name="es_promo" value="1" <?php echo !empty($editProduct['es_promo']) ? 'checked' : ''; ?>> Marcar como producto en promoción
                    </label>
                </div>
                <button type="submit" class="btn btn-primary"><?php echo $editProduct ? 'Actualizar producto' : 'Guardar producto'; ?></button>
            </div>
        </form>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?php echo (int) $producto['id']; ?></td>
                            <td><?php echo e($producto['codigo']); ?></td>
                            <td><?php echo e($producto['nombre']); ?></td>
                            <td><?php echo e($producto['categoria']); ?></td>
                            <td><?php echo format_money($producto['precio']); ?></td>
                            <td><?php echo (int) $producto['stock']; ?></td>
                            <td><?php echo e($producto['estado']); ?></td>
                            <td>
                                <a href="productos.php?edit=<?php echo (int) $producto['id']; ?>" class="btn btn-outline">Editar</a>
                                <a href="productos.php?delete=<?php echo (int) $producto['id']; ?>" class="btn btn-secondary" onclick="return confirm('¿Eliminar producto?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
