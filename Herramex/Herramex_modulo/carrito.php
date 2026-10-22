<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    validate_csrf();
    $productId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $quantity = isset($_POST['quantity']) ? max(1, (int) $_POST['quantity']) : 1;
    add_to_cart($productId, $quantity);
    redirect('carrito.php');
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

    if ($action === 'add' && $id > 0) {
        add_to_cart($id, 1);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            http_response_code(200);
            echo 'ok';
            exit;
        }
        redirect('carrito.php');
    }

    if ($action === 'update' && $id > 0) {
        $quantity = isset($_GET['qty']) ? max(1, (int) $_GET['qty']) : 1;
        update_cart_item($id, $quantity);
        redirect('carrito.php');
    }

    if ($action === 'remove' && $id > 0) {
        remove_from_cart($id);
        redirect('carrito.php');
    }
}

$cart = get_cart();
$total = cart_total();
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Carrito de compras</h2>
            <p>Revisa tus productos antes de confirmar la compra.</p>
        </div>

        <?php if (empty($cart)): ?>
            <div class="info-card" style="padding:2rem; text-align:center;">
                <h3>Tu carrito está vacío</h3>
                <p>Explora el catálogo y agrega herramientas o materiales para empezar tu compra.</p>
                <a href="catalogo.php" class="btn btn-primary">Ver catálogo</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div class="form-grid">
                    <?php foreach ($cart as $item): ?>
                        <?php
                        $itemId = (int) $item['id'];
                        $product = get_product_by_id($itemId);
                        $image = $product['imagen'] ?? '';
                        ?>
                        <div class="cart-item">
                            <img src="<?php echo get_product_image($image); ?>" alt="<?php echo e($item['nombre']); ?>">
                            <div>
                                <h3><?php echo e($item['nombre']); ?></h3>
                                <p class="muted">Precio unitario: <?php echo format_money($item['precio']); ?></p>
                                <div class="qty-controls">
                                    <a href="carrito.php?action=update&id=<?php echo $itemId; ?>&qty=<?php echo max(1, (int)$item['cantidad'] - 1); ?>" class="btn btn-outline" style="padding:0.5rem 0.8rem;">-</a>
                                    <span><?php echo (int) $item['cantidad']; ?></span>
                                    <a href="carrito.php?action=update&id=<?php echo $itemId; ?>&qty=<?php echo ((int)$item['cantidad'] + 1); ?>" class="btn btn-outline" style="padding:0.5rem 0.8rem;">+</a>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <strong><?php echo format_money((float)$item['precio'] * (int)$item['cantidad']); ?></strong>
                                <div style="margin-top:0.8rem;">
                                    <a href="carrito.php?action=remove&id=<?php echo $itemId; ?>" class="link-muted">Eliminar</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <aside class="summary-box">
                    <h3>Resumen</h3>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <strong><?php echo format_money($total); ?></strong>
                    </div>
                    <div class="summary-row">
                        <span>Envío</span>
                        <strong><?php echo format_money(0); ?></strong>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span><?php echo format_money($total); ?></span>
                    </div>
                    <div style="margin-top:1rem;">
                        <a href="checkout.php" class="btn btn-primary" style="width:100%;">Proceder al pago</a>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
