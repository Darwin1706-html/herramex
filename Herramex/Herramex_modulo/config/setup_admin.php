<?php
require_once __DIR__ . '/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $nombre = trim($_POST['nombre'] ?? 'Administrador HERRAMEX');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($email === '' || $password === '' || $confirmPassword === '') {
        echo '<p>Debes completar email y contraseña.</p>';
        exit;
    }

    if ($password !== $confirmPassword) {
        echo '<p>Las contraseñas no coinciden.</p>';
        exit;
    }

    $pdo = getDb();
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $existing = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
    $existing->execute([':email' => $email]);
    $user = $existing->fetch();

    if ($user) {
        $stmt = $pdo->prepare('UPDATE usuarios SET nombre = :nombre, password = :password, rol = :rol, estado = :estado WHERE email = :email');
        $stmt->execute([
            ':nombre' => $nombre,
            ':password' => $hash,
            ':rol' => 'admin',
            ':estado' => 'activo',
            ':email' => $email,
        ]);
        echo '<h2>Usuario administrador actualizado correctamente.</h2>';
    } else {
        $stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (:nombre, :email, :password, :rol, :estado)');
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password' => $hash,
            ':rol' => 'admin',
            ':estado' => 'activo',
        ]);
        echo '<h2>Usuario administrador creado correctamente.</h2>';
    }

    echo '<p>Cuenta creada/actualizada con acceso administrativo.</p>';
    echo '<p>Email: ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<p>Contraseña: Ha sido generada con <code>password_hash()</code> y no se registra en el código fuente.</p>';
    echo '<p><a href="../index.php">Volver a la tienda</a></p>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del administrador</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7fb; } 
        .setup-box { max-width: 480px; margin: 4rem auto; background: #fff; border-radius: 18px; padding: 2rem; box-shadow: 0 20px 35px rgba(13,45,75,0.12); }
        .setup-box h2 { color: #0d2d4b; margin-top: 0; }
        .setup-box form { display: grid; gap: 1rem; }
        input { width: 100%; padding: 0.85rem 0.9rem; border-radius: 12px; border: 1px solid #dfe7f0; }
        button { width: 100%; }
    </style>
</head>
<body>
    <div class="setup-box">
        <h2>Crear o actualizar administrador</h2>
        <p>Usa una contraseña segura y distinta a la de producción.</p>
        <form method="post">
            <div>
                <label for="nombre">Nombre</label>
                <input id="nombre" type="text" name="nombre" value="Administrador HERRAMEX">
            </div>
            <div>
                <label for="email">Correo electrónico</label>
                <input id="email" type="email" name="email" value="admin@herramex.mx" required>
            </div>
            <div>
                <label for="password">Contraseña</label>
                <input id="password" type="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Confirmar contraseña</label>
                <input id="confirm_password" type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Guardar administrador</button>
        </form>
    </div>
</body>
</html>
