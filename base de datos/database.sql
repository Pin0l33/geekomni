CREATE DATABASE IF NOT EXISTS geek_omniverse;
USE geek_omniverse;


CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    rol ENUM('usuario', 'admin', 'redactor') DEFAULT 'usuario'
);


CREATE TABLE contenido (
    id_contenido INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    tipo ENUM('juego', 'pelicula', 'serie', 'comic', 'personaje') NOT NULL,
    universo ENUM('marvel', 'dc', 'mcu', 'dcu', 'independiente', 'n/a') DEFAULT 'n/a',
    fecha_lanzamiento DATE,
    imagen_url VARCHAR(255),
    calificacion_redaccion DECIMAL(3,1) DEFAULT NULL,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE comentarios (
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_contenido INT DEFAULT NULL, -- Puede ser NULL si es un comentario general en "Comunidad"
    categoria ENUM('juegos', 'marvel', 'dc', 'anime_manga', 'general') DEFAULT 'general',
    cuerpo TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_contenido) REFERENCES contenido(id_contenido) ON DELETE CASCADE
);


CREATE TABLE favoritos (
    id_favorito INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_contenido INT NOT NULL,
    fecha_guardado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(id_usuario, id_contenido), -- Evita que un usuario guarde lo mismo dos veces
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_contenido) REFERENCES contenido(id_contenido) ON DELETE CASCADE
);


CREATE TABLE calificaciones_usuarios (
    id_calificacion INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_contenido INT NOT NULL,
    puntuacion TINYINT NOT NULL CHECK (puntuacion BETWEEN 1 AND 10),
    fecha_calificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(id_usuario, id_contenido), -- Un usuario solo puede puntuar un contenido una vez
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_contenido) REFERENCES contenido(id_contenido) ON DELETE CASCADE
);