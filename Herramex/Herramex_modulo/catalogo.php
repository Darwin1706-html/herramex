<?php
require_once __DIR__ . '/includes/header.php';

$search = trim($_GET['q'] ?? '');
$categoryId = isset($_GET['categoria']) ? (int) $_GET['categoria'] : null;
$onlyPromo = !empty($_GET['oferta']);

$categories = get_categories();
$products = get_products_list([
    'categoria_id' => $categoryId,
    'search' => $search,
]);

if ($onlyPromo) {
    $products = array_filter($products, function ($product) {
        return !empty($product['es_promo']);
    });
}
?>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Catálogo de productos</h2>
            <p>Encuentra materiales, herramientas y soluciones para cada necesidad.</p>
        </div>

        <div class="grid" style="grid-template-columns: 260px 1fr; gap: 2rem; align-items:start;">
            <aside class="info-card">
                <h3>Filtrar por categoría</h3>
                <div class="form-grid">
                    <a href="catalogo.php" class="btn btn-outline">Todas</a>
                    <?php foreach ($categories as $category): ?>
                        <a href="catalogo.php?categoria=<?php echo (int) $category['id']; ?>" class="btn btn-secondary" style="justify-content:flex-start; width:100%;">
                            <?php echo e($category['nombre']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div>
                <?php if (empty($products)): ?>
                    <div class="info-card" style="padding:2rem;">
                        <h3>No se encontraron productos</h3>
                        <p>Intenta con otro término o selecciona otra categoría.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-4">
                        <?php foreach ($products as $product): ?>
                            <?php
                            $stockClass = 'stock-ok';
                            if ($product['stock'] <= 5 && $product['stock'] > 0) {
                                $stockClass = 'stock-low';
                            } elseif ($product['stock'] <= 0) {
                                $stockClass = 'stock-out';
                            }
                            ?>
                            <article class="product-card">
                                <div class="product-image" style="background-image:url('<?php echo get_product_image($product['imagen']); ?>');"></div>
                                <div class="product-info">
                                    <div class="meta-line">
                                        <span><?php echo e($product['categoria'] ?? 'General'); ?></span>
                                        <span class="stock-pill <?php echo $stockClass; ?>"><?php echo $product['stock'] > 0 ? 'En stock' : 'Agotado'; ?></span>
                                    </div>
                                    <h3><?php echo e($product['nombre']); ?></h3>
                                    <div class="meta-line">
                                        <span><?php echo e($product['codigo']); ?></span>
                                        <span class="price"><?php echo format_money($product['precio']); ?></span>
                                    </div>
                                    <div class="product-actions">
                                        <a href="producto.php?id=<?php echo (int) $product['id']; ?>" class="btn btn-outline">Detalle</a>
                                        <button class="btn btn-primary" type="button" data-quick-add="<?php echo (int) $product['id']; ?>">Agregar</button>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
