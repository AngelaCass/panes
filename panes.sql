CREATE DATABASE distribucion_de_panes1;
USE distribucion_de_panes1;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_del_pan VARCHAR(40) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    cantidad INT NOT NULL,
    fecha_elaboracion DATE NOT NULL,
    fecha_entrega DATE NOT NULL,
    sucursal VARCHAR(45) NOT NULL
);
INSERT INTO usuarios (username, password) VALUES
('Johnny', '5520731610'),
('Cass', '5514898179'),   
('Alexis', '5565110776'), 
('Jesus', '5611200769'); 


SELECT * FROM distribucion_de_panes1.pedidos;

DROP TABLE distribucion_de_panes1.pedidos;
