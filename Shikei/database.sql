CREATE DATABASE shikei_db;
USE shikei_db;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'cliente') DEFAULT 'admin'
);

INSERT INTO usuarios (usuario, password, rol) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(50),
    imagen VARCHAR(255) DEFAULT 'img/placeholder.jpg'
);

INSERT INTO productos (nombre, descripcion, precio, categoria, imagen) VALUES 
('Ramen SHIKEI', 'Caldo tonkotsu con chashu, huevo y cebollin.', 12.50, 'Platos Fuertes', 'img/placeholder.jpg'),
('Gyozas', 'Empanadillas japonesas de cerdo y verduras (6 pzas).', 6.00, 'Entradas', 'img/placeholder.jpg'),
('Matcha Latte', 'Te verde matcha premium con leche.', 4.50, 'Bebidas', 'img/placeholder.jpg');

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_nombre VARCHAR(100) NOT NULL,
    mesa INT NOT NULL,
    total DECIMAL(10, 2) NOT NULL,
    estado ENUM('pendiente', 'en_espera', 'entregado', 'cancelado') DEFAULT 'pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE detalle_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT,
    producto_id INT,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(id)
);