<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validate_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        set_message('danger', 'Debes ingresar email y contraseña.');
        redirect('login.php');
    }

    $stmt = getDb()->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || (!password_verify($password, $user['password']) && $user['password'] !== $password)) {
        set_message('danger', 'Credenciales incorrectas.');
        redirect('login.php');
    }

    if ($user['password'] === $password && !password_verify($password, $user['password'])) {
        $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        $hashStmt = getDb()->prepare('UPDATE usuarios SET password = :password WHERE id = :id');
        $hashStmt->execute([':password' => $user['password'], ':id' => $user['id']]);
    }

    login_user($user);
    set_message('success', 'Sesión iniciada correctamente.');
    redirect('index.php');
}
?>

<section class="section">
    <div class="container form-shell">
        <div class="form-card">
            <h2>Iniciar sesión</h2>
            <form method="post" class="form-grid">
                <?php echo csrf_field(); ?>
                <div class="form-row">
                    <label for="email">Correo electrónico</label>
                    <input id="email" name="email" type="email" required>
                </div>
                <div class="form-row">
                    <label for="password">Contraseña</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Entrar</button>
            </form>
        </div>

        <div class="info-card" style="padding:2rem;">
            <h3>¿No tienes cuenta?</h3>
            <p>Crea tu cuenta para comprar, guardar tus compras y gestionar pedidos.</p>
            <a href="registro.php" class="btn btn-dark">Registrarme</a>
            <div style="margin-top:1.5rem;">
                <p><strong>Cuenta de demostración</strong></p>
                <p>Correo: ejemplo@gmail.com</p>
                <p>Contraseña: ejemplo123</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
