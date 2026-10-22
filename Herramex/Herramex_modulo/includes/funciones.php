<?php

require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function format_money($amount): string
{
    return '$' . number_format((float) $amount, 2, '.', ',');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_message(string $type, string $message): void
{
    $_SESSION['flash_messages'][] = ['type' => $type, 'message' => $message];
}

function render_messages(): void
{
    if (empty($_SESSION['flash_messages'])) {
        return;
    }

    foreach ($_SESSION['flash_messages'] as $flash) {
        $type = e($flash['type']);
        $message = e($flash['message']);
        echo '<div class="alert alert-' . $type . '">' . $message . '</div>';
    }

    unset($_SESSION['flash_messages']);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function validate_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        set_message('danger', 'La sesión expiró o el formulario es inválido. Inténtalo de nuevo.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'index.php');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_message('warning', 'Debes iniciar sesión para continuar.');
        redirect('login.php');
    }
}

function require_role(array $roles): void
{
    $user = current_user();
    if (!$user || !in_array($user['rol'], $roles, true)) {
        set_message('danger', 'No tienes permisos para acceder a esta sección.');
        redirect('index.php');
    }
}

function get_product_image($image): string
{
    if (empty($image)) {
        return 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=900&q=80';
    }

    if (strpos($image, 'http') === 0) {
        return $image;
    }

    return 'assets/img/' . ltrim($image, '/');
}

function get_cart(): array
{
    return $_SESSION['cart'] ?? [];
}

function add_to_cart(int $productId, int $quantity = 1): void
{
    $pdo = getDb();

    $stmt = $pdo->prepare('SELECT id, nombre, precio, stock, estado FROM productos WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $productId]);
    $product = $stmt->fetch();

    if (!$product || $product['estado'] !== 'activo') {
        set_message('warning', 'El producto no está disponible.');
        return;
    }

    $cart = get_cart();
    $productKey = (string) $productId;

    if (isset($cart[$productKey])) {
        $newQty = $cart[$productKey]['cantidad'] + $quantity;
    } else {
        $newQty = $quantity;
    }

    if ($newQty > (int) $product['stock']) {
        $newQty = (int) $product['stock'];
        set_message('warning', 'Solo quedan ' . $product['stock'] . ' unidades disponibles.');
    }

    $cart[$productKey] = [
        'id' => (int) $product['id'],
        'nombre' => $product['nombre'],
        'precio' => (float) $product['precio'],
        'cantidad' => max(1, $newQty),
        'stock' => (int) $product['stock'],
    ];

    $_SESSION['cart'] = $cart;
    set_message('success', 'Producto agregado al carrito.');
}

function update_cart_item(int $productId, int $quantity): void
{
    $cart = get_cart();

    if (!isset($cart[$productId])) {
        return;
    }

    $cart[$productId]['cantidad'] = max(1, $quantity);
    $_SESSION['cart'] = $cart;
}

function remove_from_cart(int $productId): void
{
    $cart = get_cart();
    unset($cart[$productId]);
    $_SESSION['cart'] = $cart;
}

function cart_total(): float
{
    $total = 0.0;
    foreach (get_cart() as $item) {
        $total += (float) $item['precio'] * (int) $item['cantidad'];
    }
    return $total;
}

function cart_count(): int
{
    $count = 0;
    foreach (get_cart() as $item) {
        $count += (int) $item['cantidad'];
    }
    return $count;
}

function clear_cart(): void
{
    $_SESSION['cart'] = [];
}

function get_product_by_id(int $id): ?array
{
    $stmt = getDb()->prepare('SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.id = :id LIMIT 1');
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();
    return $product ?: null;
}

function get_products_list(array $filters = []): array
{
    $pdo = getDb();
    $sql = 'SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id = p.categoria_id WHERE p.estado = :estado';
    $params = [':estado' => 'activo'];

    if (!empty($filters['categoria_id'])) {
        $sql .= ' AND p.categoria_id = :categoria_id';
        $params[':categoria_id'] = (int) $filters['categoria_id'];
    }

    if (!empty($filters['search'])) {
        $sql .= ' AND p.nombre LIKE :search';
        $params[':search'] = '%' . trim($filters['search']) . '%';
    }

    $sql .= ' ORDER BY p.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function get_categories(): array
{
    $stmt = getDb()->query('SELECT * FROM categorias ORDER BY nombre ASC');
    return $stmt->fetchAll();
}

function get_payment_methods(): array
{
    $stmt = getDb()->query('SELECT * FROM metodos_pago WHERE estado = "activo" ORDER BY nombre ASC');
    return $stmt->fetchAll();
}

function payment_method_requires_card(array $method): bool
{
    $name = (string) ($method['nombre'] ?? '');
    return stripos($name, 'tarjeta') !== false
        || preg_match('/\b(visa|mastercard|american express|amex)\b/i', $name) === 1;
}

function validate_simulated_card(string $number, string $cvv, string $expiry): ?string
{
    $digits = preg_replace('/\D/', '', $number);
    if (strlen($digits) < 13 || strlen($digits) > 19) {
        return 'Ingresa un número de tarjeta de prueba válido (de 13 a 19 dígitos).';
    }

    $sum = 0;
    $double = false;
    for ($index = strlen($digits) - 1; $index >= 0; $index--) {
        $digit = (int) $digits[$index];
        if ($double) {
            $digit *= 2;
            if ($digit > 9) {
                $digit -= 9;
            }
        }
        $sum += $digit;
        $double = !$double;
    }
    if ($sum % 10 !== 0) {
        return 'El número de tarjeta de prueba no es válido.';
    }

    if (!preg_match('/^\d{3,4}$/', $cvv)) {
        return 'Ingresa un CVV de prueba de 3 o 4 dígitos.';
    }

    if (!preg_match('/^(\d{4})-(0[1-9]|1[0-2])$/', $expiry, $matches)) {
        return 'Selecciona una fecha de vencimiento válida.';
    }

    $expiryMonth = $matches[1] . '-' . $matches[2];
    if ($expiryMonth < date('Y-m')) {
        return 'La tarjeta de prueba está vencida. Usa una fecha igual o posterior al mes actual.';
    }

    return null;
}

function get_dashboard_stats(): array
{
    $pdo = getDb();

    $stats = [];
    $stats['productos'] = (int) $pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();
    $stats['bajos'] = (int) $pdo->query('SELECT COUNT(*) FROM productos WHERE stock <= 5')->fetchColumn();
    $stats['clientes'] = (int) $pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
    $stats['ventas'] = (int) $pdo->query('SELECT COUNT(*) FROM ventas')->fetchColumn();
    $stats['ingresos'] = (float) $pdo->query('SELECT COALESCE(SUM(total),0) FROM ventas')->fetchColumn();

    return $stats;
}
