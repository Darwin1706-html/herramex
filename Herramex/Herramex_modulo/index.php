<?php
require_once __DIR__ . '/includes/header.php';

$categories = get_categories();
$featuredProducts = get_products_list(['categoria_id' => null, 'search' => '']);
$featuredProducts = array_slice($featuredProducts, 0, 4);
?>

<section class="hero">
    <div class="container hero-inner">
        <div class="hero-copy">
            <h1>HERRAMEX</h1>
            <p>Herramientas que construyen tu futuro. Soluciones profesionales para construcción, mantenimiento, jardinería, electricidad y más.</p>
            <div class="hero-cta">
                <a href="catalogo.php" class="btn btn-primary">Explorar productos</a>
                <a href="nosotros.php" class="btn btn-secondary">Conócenos</a>
            </div>
        </div>

        <div class="hero-card">
            <div class="hero-stat">
                <div>
                    <small>Productos</small>
                    <strong>+500</strong>
                </div>
                <span>🧰</span>
            </div>
            <div class="hero-stat">
                <div>
                    <small>Entrega</small>
                    <strong>24h</strong>
                </div>
                <span>🚚</span>
            </div>
            <div class="hero-stat">
                <div>
                    <small>Clientes</small>
                    <strong>12k</strong>
                </div>
                <span>🤝</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Categorías</h2>
            <p>Soluciones para cada proyecto, desde la obra hasta el hogar.</p>
        </div>

        <div class="grid grid-3">
            <?php foreach ($categories as $category): ?>
                <a href="catalogo.php?categoria=<?php echo (int) $category['id']; ?>" class="category-card">
                    <div class="category-image" style="background-image:url('<?php echo get_product_image($category['imagen'] ?? ''); ?>');"></div>
                    <div class="category-content">
                        <h3><?php echo e($category['nombre']); ?></h3>
                        <p><?php echo e($category['descripcion'] ?? 'Productos de alta calidad para tu proyecto.'); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="container">
        <div class="section-header">
            <h2>Productos destacados</h2>
            <p>Lo más solicitado por clientes profesionales y particulares.</p>
        </div>

        <div class="grid grid-4">
            <?php foreach ($featuredProducts as $product): ?>
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
                            <a href="producto.php?id=<?php echo (int) $product['id']; ?>" class="btn btn-outline">Ver detalle</a>
                            <button class="btn btn-primary" type="button" data-quick-add="<?php echo (int) $product['id']; ?>">Agregar</button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="banner-sale">
            <div>
                <span class="badge badge-orange">Ofertas</span>
                <h2>Promociones de esta semana</h2>
                <p>Ahorra hasta 30% en herramientas eléctricas y materiales para obra.</p>
            </div>
            <a href="catalogo.php?oferta=1" class="btn btn-primary">Ver promociones</a>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="container company-grid">
        <div>
            <div class="section-header" style="text-align:left;">
                <h2>Quiénes somos</h2>
            </div>
            <p>HERRAMEX es una ferretería con más de una década impulsando proyectos de construcción, remodelación y mantenimiento. Nuestro enfoque combina asesoría técnica, productos de calidad y atención cercana para clientes profesionales y particulares.</p>
            <ul class="about-list">
                <li>Atención especializada para obra y hogar.</li>
                <li>Precios competitivos con productos confiables.</li>
                <li>Inventario actualizado y asistencia técnica.</li>
            </ul>
        </div>
        <div class="info-card">
            <h3>Misión</h3>
            <p>Brindar herramientas, materiales y soluciones prácticas que faciliten la construcción de proyectos seguros, eficientes y sostenibles.</p>
            <h3>Visión</h3>
            <p>Ser la ferretería líder en calidad, servicio y confianza para el desarrollo de comunidades y negocios.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>Contacto</h2>
            <p>Estamos listos para ayudarte a completar tu proyecto.</p>
        </div>

        <div class="contact-box">
            <div class="info-card">
                <h3>Información</h3>
                <p>Av. Industrial 456, Colonia Obrera, León, Guanajuato.</p>
                <p>ventas@herramex.mx</p>
                <p>+52 (477) 123 4567</p>
            </div>
            <div class="info-card">
                <h3>Horario</h3>
                <p>Lunes a Viernes: 8:00 - 18:00</p>
                <p>Sábado: 9:00 - 14:00</p>
                <p>Domingo: cerrado</p>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background:#fff;">
    <div class="container">
        <div class="map-placeholder">
            <span>Ubicación de la ferretería HERRAMEX</span>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
