<?php
require_once __DIR__ . '/includes/auth.php';
logout_user();
set_message('success', 'Sesión cerrada correctamente.');
redirect('index.php');
