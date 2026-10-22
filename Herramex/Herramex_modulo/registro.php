<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $rol = 'cliente';

    if ($nombre === '' || $email === '' || $password === '' || $confirmPassword === '') {
        set_message('danger', 'Completa todos los campos requeridos.');
        redirect('registro.php');
    }

    if ($password !== $confirmPassword) {
        set_message('danger', 'Las contraseñas no coinciden.');
        redirect('registro.php');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        set_message('danger', 'El correo electrónico no es válido.');
        redirect('registro.php');
    }

    $stmt = getDb()->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        set_message('danger', 'Ya existe una cuenta registrada con ese correo.');
        redirect('registro.php');
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $insert = getDb()->prepare('INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (:nombre, :email, :password, :rol, :estado)');
    $insert->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':password' => $hashedPassword,
        ':rol' => $rol,
        ':estado' => 'activo',
    ]);

    $userId = (int) getDb()->lastInsertId();
    $user = [
        'id' => $userId,
        'nombre' => $nombre,
        'email' => $email,
        'rol' => $rol,
    ];

    login_user($user);
    set_message('success', 'Cuenta creada correctamente.');
    redirect('index.php');
}
?>

<section class="section">
    <div class="container form-shell">
        <div class="form-card">
            <h2>Crear cuenta</h2>
            <form method="post" class="form-grid">
                <?php echo csrf_field(); ?>
                <div class="form-row">
                    <label for="nombre">Nombre completo</label>
                    <input id="nombre" name="nombre" type="text" required>
                </div>
                <div class="form-row">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div class="form-row row-2">
                    <div>
                        <label for="password">Contraseña</label>
                        <input id="password" name="password" type="password" required>
                    </div>
                    <div>
                        <label for="confirm_password">Confirmar contraseña</label>
                        <input id="confirm_password" name="confirm_password" type="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Registrarme</button>
            </form>
        </div>

        <div class="info-card" style="padding:2rem;">
            <h3>Beneficios</h3>
            <ul class="about-list">
                <li>Compra de forma rápida y segura.</li>
                <li>Historial y seguimiento de pedidos.</li>
                <li>Acceso a promociones y ofertas del sitio.</li>
            </ul>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
