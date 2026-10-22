<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

if (empty(get_cart())) {
    set_message('warning', 'Tu carrito está vacío.');
    redirect('catalogo.php');
}

$paymentMethods = get_payment_methods();
$cart = get_cart();
$total = cart_total();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $user = current_user();
    $name = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $metodoPagoId = (int) ($_POST['metodo_pago_id'] ?? 0);
    $selectedPaymentMethod = null;
    foreach ($paymentMethods as $method) {
        if ((int) $method['id'] === $metodoPagoId) {
            $selectedPaymentMethod = $method;
            break;
        }
    }

    if ($name === '' || $email === '' || $telefono === '' || $direccion === '' || !$selectedPaymentMethod) {
        set_message('danger', 'Completa todos los datos para continuar con la compra.');
        redirect('checkout.php');
    }

    if (payment_method_requires_card($selectedPaymentMethod)) {
        $cardError = validate_simulated_card(
            trim($_POST['numero_tarjeta'] ?? ''),
            trim($_POST['cvv'] ?? ''),
            trim($_POST['vencimiento'] ?? '')
        );
        if ($cardError !== null) {
            set_message('danger', $cardError);
            redirect('checkout.php');
        }
    }

    $pdo = getDb();
    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare('INSERT INTO clientes (usuario_id, nombre, email, telefono, direccion, estado) VALUES (:usuario_id, :nombre, :email, :telefono, :direccion, :estado) ON DUPLICATE KEY UPDATE nombre = VALUES(nombre), email = VALUES(email), telefono = VALUES(telefono), direccion = VALUES(direccion)');
        $stmt->execute([
            ':usuario_id' => $user['id'],
            ':nombre' => $name,
            ':email' => $email,
            ':telefono' => $telefono,
            ':direccion' => $direccion,
            ':estado' => 'activo',
        ]);

        $clienteId = (int) $pdo->lastInsertId();
        if ($clienteId <= 0) {
            $clienteStmt = $pdo->prepare('SELECT id FROM clientes WHERE usuario_id = :usuario_id LIMIT 1');
            $clienteStmt->execute([':usuario_id' => $user['id']]);
            $cliente = $clienteStmt->fetch();
            $clienteId = (int) ($cliente['id'] ?? 0);
        }

        $ventaStmt = $pdo->prepare('INSERT INTO ventas (usuario_id, cliente_id, metodo_pago_id, total, estado, fecha_venta) VALUES (:usuario_id, :cliente_id, :metodo_pago_id, :total, :estado, NOW())');
        $ventaStmt->execute([
            ':usuario_id' => $user['id'],
            ':cliente_id' => $clienteId,
            ':metodo_pago_id' => $metodoPagoId,
            ':total' => $total,
            ':estado' => 'completada',
        ]);

        $ventaId = (int) $pdo->lastInsertId();

        foreach ($cart as $item) {
            $productId = (int) $item['id'];
            $qty = (int) $item['cantidad'];
            $product = get_product_by_id($productId);

            if (!$product || $product['stock'] < $qty) {
                throw new RuntimeException('La cantidad solicitada supera el inventario disponible para ' . $item['nombre']);
            }

            $stmtDetalle = $pdo->prepare('INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario, :subtotal)');
            $stmtDetalle->execute([
                ':venta_id' => $ventaId,
                ':producto_id' => $productId,
                ':cantidad' => $qty,
                ':precio_unitario' => $item['precio'],
                ':subtotal' => $item['precio'] * $qty,
            ]);

            $stockUpdate = $pdo->prepare('UPDATE productos SET stock = stock - :cantidad WHERE id = :id');
            $stockUpdate->execute([
                ':cantidad' => $qty,
                ':id' => $productId,
            ]);
        }

        $pdo->commit();
        clear_cart();
        set_message('success', 'Compra registrada correctamente.');
        redirect('index.php');
    } catch (Throwable $e) {
        $pdo->rollBack();
        set_message('danger', $e->getMessage());
        redirect('checkout.php');
    }
}
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Confirmar compra</h2>
            <p>Completa tus datos y confirma la compra.</p>
        </div>

        <div class="form-shell">
            <div class="form-card">
                <form method="post" class="form-grid">
                    <?php echo csrf_field(); ?>
                    <div class="form-row">
                        <label for="nombre">Nombre completo</label>
                        <input id="nombre" name="nombre" type="text" value="<?php echo e(current_user()['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="form-row row-2">
                        <div>
                            <label for="email">Correo</label>
                            <input id="email" name="email" type="email" value="<?php echo e(current_user()['email'] ?? ''); ?>" required>
                        </div>
                        <div>
                            <label for="telefono">Teléfono</label>
                            <input id="telefono" name="telefono" type="text" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="direccion">Dirección</label>
                        <textarea id="direccion" name="direccion" rows="3" required></textarea>
                    </div>
                    <div class="form-row">
                        <label for="metodo_pago_id">Método de pago</label>
                        <select id="metodo_pago_id" name="metodo_pago_id" data-payment-method-select required>
                            <option value="">Selecciona un método</option>
                            <?php foreach ($paymentMethods as $method): ?>
                                <option value="<?php echo (int) $method['id']; ?>" data-requires-card="<?php echo payment_method_requires_card($method) ? 'true' : 'false'; ?>"><?php echo e($method['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <fieldset class="form-grid" data-card-fields hidden>
                        <legend>Datos de tarjeta (simulación)</legend>
                        <p>Usa únicamente datos ficticios. Ejemplo de prueba: 4111 1111 1111 1111, CVV 123 y una fecha vigente.</p>
                        <div class="form-row">
                            <label for="numero_tarjeta">Número de tarjeta</label>
                            <input id="numero_tarjeta" name="numero_tarjeta" type="text" inputmode="numeric" autocomplete="cc-number" maxlength="23" placeholder="0000 0000 0000 0000" data-card-required>
                        </div>
                        <div class="form-row row-2">
                            <div>
                                <label for="cvv">CVV</label>
                                <input id="cvv" name="cvv" type="password" inputmode="numeric" autocomplete="cc-csc" maxlength="4" placeholder="123" data-card-required>
                            </div>
                            <div>
                                <label for="vencimiento">Fecha de vencimiento</label>
                                <input id="vencimiento" name="vencimiento" type="month" autocomplete="cc-exp" data-card-required>
                            </div>
                        </div>
                    </fieldset>
                    <button type="submit" class="btn btn-primary">Confirmar compra</button>
                </form>
            </div>

            <aside class="summary-box">
                <h3>Resumen del pedido</h3>
                <?php foreach ($cart as $item): ?>
                    <div class="summary-row">
                        <span><?php echo e($item['nombre']); ?> x <?php echo (int) $item['cantidad']; ?></span>
                        <strong><?php echo format_money((float) $item['precio'] * (int) $item['cantidad']); ?></strong>
                    </div>
                <?php endforeach; ?>
                <div class="summary-row total">
                    <span>Total</span>
                    <span><?php echo format_money($total); ?></span>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
