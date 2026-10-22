CREATE DATABASE IF NOT EXISTS herramex_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE herramex_db;

CREATE TABLE empresa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    slogan VARCHAR(255) NOT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    sitio_web VARCHAR(150) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin','empleado','cliente') NOT NULL DEFAULT 'cliente',
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_usuarios_email (email)
) ENGINE=InnoDB;

CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    nombre VARCHAR(150) NOT NULL,
    apellido VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30) DEFAULT NULL,
    puesto VARCHAR(120) DEFAULT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_empleados_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telefono VARCHAR(30) DEFAULT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_clientes_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    contacto VARCHAR(150) DEFAULT NULL,
    email VARCHAR(150) DEFAULT NULL,
    telefono VARCHAR(30) DEFAULT NULL,
    direccion VARCHAR(255) DEFAULT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255) DEFAULT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categoria_nombre (nombre)
) ENGINE=InnoDB;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(180) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria_id INT NOT NULL,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255) DEFAULT NULL,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    es_promo TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_productos_categoria (categoria_id),
    INDEX idx_productos_nombre (nombre),
    INDEX idx_productos_stock (stock)
) ENGINE=InnoDB;

CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    estado ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_metodo_pago_nombre (nombre)
) ENGINE=InnoDB;

CREATE TABLE ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    cliente_id INT NOT NULL,
    metodo_pago_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('completada','pendiente','cancelada') NOT NULL DEFAULT 'completada',
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ventas_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_ventas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_ventas_metodo FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_ventas_fecha (fecha_venta)
) ENGINE=InnoDB;

CREATE TABLE detalle_venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venta_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_detalle_venta FOREIGN KEY (venta_id) REFERENCES ventas(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_detalle_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_detalle_venta_id (venta_id)
) ENGINE=InnoDB;

INSERT INTO empresa (nombre, slogan, direccion, telefono, email, sitio_web) VALUES
('HERRAMEX', 'Herramientas que construyen tu futuro', 'Av. Industrial 456, León, Guanajuato', '+52 (477) 123 4567', 'ventas@herramex.mx', 'https://herramex.local');

INSERT INTO categorias (nombre, descripcion, imagen, estado) VALUES
('Herramientas manuales', 'Martillos, llaves, destornilladores y más.', 'https://images.unsplash.com/photo-1581149481858-b2c7e5d6b64f?auto=format&fit=crop&w=900&q=80', 'activo'),
('Herramientas eléctricas', 'Taladros, sierras y accesorios eléctricos.', 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=900&q=80', 'activo'),
('Materiales de construcción', 'Cemento, grava, arena, bloques y reforzamiento.', 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=900&q=80', 'activo'),
('Pinturas y accesorios', 'Pinturas, brochas, rodillos y materiales para acabado.', 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&w=900&q=80', 'activo'),
('Fontanería', 'Tuberías, llaves y accesorios de instalación.', 'https://images.unsplash.com/photo-1621905251918-48416bd8575a?auto=format&fit=crop&w=900&q=80', 'activo'),
('Electricidad', 'Cableado, breakers, contactos, tomas y accesorios.', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80', 'activo'),
('Tornillería y fijaciones', 'Tornillos, clavos, anclajes y sujeciones.', 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?auto=format&fit=crop&w=900&q=80', 'activo'),
('Jardinería y herramientas agrícolas', 'Pala, poda, mangueras y equipos de campo.', 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=900&q=80', 'activo'),
('Seguridad industrial', 'Equipos de protección y seguridad en obra.', 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=900&q=80', 'activo');

INSERT INTO metodos_pago (nombre, descripcion, estado) VALUES
('Efectivo', 'Pago en efectivo en tienda o entrega.', 'activo'),
('Pago simulado con tarjeta', 'Método de prueba para simulación de pago.', 'activo'),
('Tarjeta Visa', 'Tarjeta Visa para simulación de pago.', 'activo'),
('Tarjeta Mastercard', 'Tarjeta Mastercard para simulación de pago.', 'activo'),
('Tarjeta Banco Agrícola', 'Tarjeta de Banco Agrícola para simulación de pago.', 'activo'),
('Tarjeta Banco Cuscatlán', 'Tarjeta de Banco Cuscatlán para simulación de pago.', 'activo'),
('Tarjeta BAC Credomatic', 'Tarjeta BAC Credomatic para simulación de pago.', 'activo'),
('Tarjeta Davivienda', 'Tarjeta Davivienda para simulación de pago.', 'activo'),
('PayPal (simulado)', 'Pago electrónico ficticio para el proyecto.', 'activo'),
('Neki (simulado)', 'Billetera digital ficticia para el proyecto.', 'activo'),
('Transferencia bancaria', 'Transferencia bancaria local simulada.', 'activo'),
('Transferencia Banco Agrícola', 'Transferencia bancaria simulada.', 'activo'),
('Transferencia Banco Cuscatlán', 'Transferencia bancaria simulada.', 'activo');

INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES
('Empleado Demo', 'empleado@herramex.mx', '$2y$10$dummyhashplaceholderforsecuresetup', 'empleado', 'activo'),
('Cliente Demo', 'cliente@herramex.mx', '$2y$10$dummyhashplaceholderforsecuresetup', 'cliente', 'activo');

INSERT INTO empleados (usuario_id, nombre, apellido, email, telefono, puesto, estado) VALUES
(1, 'Ana', 'Sánchez', 'empleado@herramex.mx', '4771234568', 'Supervisor de ventas', 'activo');

INSERT INTO clientes (usuario_id, nombre, email, telefono, direccion, estado) VALUES
(2, 'Cliente Demo', 'cliente@herramex.mx', '4771234569', 'Calle Central 123, León', 'activo');

INSERT INTO productos (codigo, nombre, descripcion, categoria_id, precio, stock, imagen, estado, es_promo) VALUES
('HM-001', 'Martillo de carpintero 16 oz', 'Martillo robusto para carpintería y trabajos generales de construcción.', 1, 320.00, 12, 'https://images.unsplash.com/photo-1581149481858-b2c7e5d6b64f?auto=format&fit=crop&w=1200&q=80', 'activo', 1),
('HE-002', 'Taladro percutor 650W', 'Taladro de alto rendimiento para perforación y atornillado en materiales duros.', 2, 1850.00, 8, 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1200&q=80', 'activo', 1),
('MC-003', 'Cemento gris 25 kg', 'Cemento de uso general para obra y reconstrucción.', 3, 180.00, 40, 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1200&q=80', 'activo', 0),
('PA-004', 'Pintura látex interior 18L', 'Pintura de acabado liso para interiores con excelente cobertura.', 4, 820.00, 10, 'https://images.unsplash.com/photo-1527515637462-cff94eecc1ac?auto=format&fit=crop&w=1200&q=80', 'activo', 1),
('FO-005', 'Llave de paso 1/2', 'Llave de paso de calidad para instalaciones domésticas.', 5, 240.00, 15, 'https://images.unsplash.com/photo-1621905251918-48416bd8575a?auto=format&fit=crop&w=900&q=80', 'activo', 0),
('EL-006', 'Cable THW 2.5 mm', 'Cable eléctrico apto para instalaciones domésticas y comerciales.', 6, 65.00, 50, 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80', 'activo', 0),
('TF-007', 'Juego de tornillos 100 piezas', 'Set de tornillería de alta resistencia para madera y metal.', 7, 145.00, 70, 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?auto=format&fit=crop&w=900&q=80', 'activo', 0),
('JG-008', 'Pala de jardinería', 'Pala ligera con mango resistente para jardinería y paisajismo.', 8, 420.00, 6, 'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?auto=format&fit=crop&w=900&q=80', 'activo', 1),
('SI-009', 'Casco de seguridad industrial', 'Protección de cabeza con alta resistencia y ajuste cómodo.', 9, 620.00, 9, 'https://images.unsplash.com/photo-1556157382-97eda2d62296?auto=format&fit=crop&w=1200&q=80', 'activo', 0),
('HE-010', 'Sierra circular 7 1/4', 'Sierra eléctrica precisa para cortes rápidos y exactos.', 2, 2100.00, 4, 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1200&q=80', 'activo', 0);

INSERT INTO ventas (usuario_id, cliente_id, metodo_pago_id, total, estado, fecha_venta) VALUES
(2, 1, 1, 1480.00, 'completada', NOW()),
(2, 1, 2, 950.00, 'completada', NOW());

INSERT INTO detalle_venta (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES
(1, 1, 2, 320.00, 640.00),
(1, 6, 10, 65.00, 650.00),
(1, 3, 2, 180.00, 360.00),
(2, 4, 1, 820.00, 820.00),
(2, 7, 1, 145.00, 130.00);
