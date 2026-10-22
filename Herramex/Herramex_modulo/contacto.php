<?php
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if ($nombre === '' || $email === '' || $mensaje === '') {
        set_message('danger', 'Completa todos los campos del formulario.');
        redirect('contacto.php');
    }

    set_message('success', 'Gracias por contactarnos. En breve te responderemos.');
    redirect('contacto.php');
}
?>

<section class="section">
    <div class="container form-shell">
        <div class="form-card">
            <h2>Contáctanos</h2>
            <form method="post" class="form-grid">
                <?php echo csrf_field(); ?>
                <div class="form-row">
                    <label for="nombre">Nombre</label>
                    <input id="nombre" name="nombre" type="text" required>
                </div>
                <div class="form-row">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div class="form-row">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar mensaje</button>
            </form>
        </div>

        <div class="info-card" style="padding:2rem;">
            <h3>Información de contacto</h3>
            <p><strong>Dirección:</strong> Av. Industrial 456, León, Gto.</p>
            <p><strong>Teléfono:</strong> +52 (477) 123 4567</p>
            <p><strong>Email:</strong> ventas@herramex.mx</p>
            <p><strong>Horario:</strong> L-V 8:00 - 18:00</p>
            <div class="map-placeholder" style="margin-top:1rem; min-height:180px;">
                <span>Ubicación HERRAMEX</span>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
