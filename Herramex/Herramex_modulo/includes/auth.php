<?php

require_once __DIR__ . '/funciones.php';

function user_is_admin(): bool
{
    $user = current_user();
    return $user && $user['rol'] === 'admin';
}

function user_is_employee(): bool
{
    $user = current_user();
    return $user && in_array($user['rol'], ['admin', 'empleado'], true);
}

function require_admin(): void
{
    if (!user_is_admin()) {
        set_message('danger', 'Necesitas permisos de administrador para entrar aquí.');
        redirect('../index.php');
    }
}

function require_employee_or_admin(): void
{
    if (!user_is_employee()) {
        set_message('danger', 'No tienes permisos para gestionar esta sección.');
        redirect('../index.php');
    }
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'nombre' => $user['nombre'],
        'email' => $user['email'],
        'rol' => $user['rol'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];
    session_destroy();
}
