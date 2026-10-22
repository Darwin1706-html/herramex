# HERRAMEX

Sistema web de ferretería para gestión de catálogo, carrito de compras y panel administrativo.

## Tecnologías

- HTML5
- CSS3
- JavaScript
- PHP 8+
- MySQL / MariaDB
- PDO con consultas preparadas

## Estructura principal

- `index.php` - Inicio de la tienda
- `catalogo.php` - Catálogo público
- `producto.php` - Detalle del producto
- `carrito.php` - Carrito de compras
- `checkout.php` - Confirmación y venta
- `login.php` - Inicio de sesión
- `registro.php` - Registro de clientes
- `admin/` - Panel administrativo
- `config/database.php` - Configuración de conexión
- `config/setup_admin.php` - Crea el usuario administrador seguro de demostración
- `sql/herramex_db.sql` - Script de base de datos

## Instalación local (XAMPP)

1. Instala XAMPP y abre Apache y MySQL.
2. Crea la base de datos desde phpMyAdmin o ejecuta el archivo SQL:
   - `sql/herramex_db.sql`
3. Coloca la carpeta `Herramex_modulo` dentro de `htdocs`.
4. Entra en el navegador: `http://localhost/Herramex_modulo/`
5. Para configurar el administrador: `http://localhost/Herramex_modulo/config/setup_admin.php`
6. Después, accede al panel administrativo en: `http://localhost/Herramex_modulo/admin/index.php`

## Configuración segura del administrador de demostración

Se recomienda crear la cuenta administrativa con el script de configuración que se encuentra en `config/setup_admin.php`.

Este proceso:

- crea o actualiza el usuario administrador;
- solicita una contraseña segura en tiempo real;
- guarda el hash con `password_hash()`;
- evita dejar contraseñas sensibles dentro del código fuente.

## Configuración del entorno

En `config/database.php` puedes ajustar las variables de entorno o editar los valores por defecto:

- `DB_HOST`
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_PORT`

Ejemplo:

```php
putenv('DB_HOST=localhost');
putenv('DB_NAME=herramex_db');
putenv('DB_USER=root');
putenv('DB_PASS=');
```

## Seguridad

- Conexión con PDO
- Consultas preparadas
- Hash de contraseñas con `password_hash()`
- Verificación con `password_verify()`
- Control de sesión y permisos por rol
- Validación de formularios

## Publicación web

Para publicarlo en un hosting compatible con PHP y MySQL:

1. Subir todos los archivos del proyecto.
2. Crear la base de datos en el hosting.
3. Importar el SQL.
4. Configurar `config/database.php` con las credenciales reales.
5. Verificar que `mod_rewrite` y permisos de escritura existan si se usan rutas amigables.

## Observaciones

- El proyecto incluye un flujo de prueba de pago y no procesa pagos reales.
- Para PHPMailer se recomienda instalarlo en el servidor y configurar SMTP en un archivo de configuración extra.
- Si se quiere activar correo real, se debe configurar `SMTP_HOST`, `SMTP_USER`, `SMTP_PASS` y `SMTP_PORT`.
