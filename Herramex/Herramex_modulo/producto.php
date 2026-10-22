<?php
require_once __DIR__ . '/includes/header.php';

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = get_product_by_id($productId);

if (!$product) {
    set_message('danger', 'El producto solicitado no existe.');
    redirect('catalogo.php');
}

$stockClass = 'stock-ok';
if ($product['stock'] <= 5 && $product['stock'] > 0) {
    $stockClass = 'stock-low';
} elseif ($product['stock'] <= 0) {
    $stockClass = 'stock-out';
}
?>

<section class="section">
    <div class="container">
        <div class="product-detail">
            <div class="product-detail-image" style="background-image:url('<?php echo get_product_image($product['imagen']); ?>');"></div>

            <div class="details-box">
                <div class="meta-line">
                    <span class="badge badge-blue"><?php echo e($product['categoria'] ?? 'General'); ?></span>
                    <?php if (!empty($product['es_promo'])): ?>
                        <span class="badge badge-orange">En promoción</span>
                    <?php endif; ?>
                </div>
                <h1><?php echo e($product['nombre']); ?></h1>
                <div class="meta-line">
                    <span>Código: <?php echo e($product['codigo']); ?></span>
                    <span class="price"><?php echo format_money($product['precio']); ?></span>
                </div>
                <div class="meta-line" style="margin-bottom:1.1rem;">
                    <span class="stock-pill <?php echo $stockClass; ?>"><?php echo $product['stock'] > 0 ? 'Disponibles: ' . $product['stock'] : 'Sin stock'; ?></span>
                    <span><?php echo e($product['estado']); ?></span>
                </div>

                <p><?php echo e($product['descripcion']); ?></p>

                <form method="post" action="carrito.php">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="<?php echo (int) $product['id']; ?>">
                    <?php echo csrf_field(); ?>
                    <div class="form-row" style="margin-bottom:1rem; max-width:180px;">
                        <label for="quantity">Cantidad</label>
                        <input type="number" id="quantity" name="quantity" min="1" max="<?php echo (int) $product['stock']; ?>" value="1">
                    </div>
                    <button type="submit" class="btn btn-primary">Agregar al carrito</button>
                </form>

                <div style="margin-top: 1rem;">
                    <a href="catalogo.php" class="btn btn-outline">Volver al catálogo</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
