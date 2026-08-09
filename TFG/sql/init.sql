-- Crear base de datos
DROP DATABASE IF EXISTS moodplanned;
CREATE DATABASE moodplanned CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE moodplanned;

-- ======================
--        USERS
-- ======================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255),   -- Imagen de perfil del usuario
    banner VARCHAR(255),          -- Imagen de banner del usuario
    bio VARCHAR(255),          -- BiografÃ­a del usuario
    points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ======================
--         PLANS
-- ======================
CREATE TABLE plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    lat DECIMAL(10,7),
    lng DECIMAL(10,7),
    direccion VARCHAR(255),       -- Nueva columna para la dirección
    image VARCHAR(255),           -- Imagen asociada al plan
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);


-- ======================
--       FAVORITES
-- ======================
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    plan_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
);

-- ======================
--        REVIEWS
-- ======================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    rating TINYINT NOT NULL,   -- 1-5
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (user_id, plan_id)
);

-- ======================
--      DAILY_MODS
-- ======================
CREATE TABLE user_mood_tracker ( 
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mood VARCHAR(50) NOT NULL,
    last_check DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS saved_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
    UNIQUE KEY unique_saved (user_id, plan_id)
);


-- Contraseña en texto: MoodPlanned2025!
-- Hash bcrypt (PASSWORD_BCRYPT / password_hash en PHP):
-- $2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K

INSERT INTO users (name, email, password_hash, profile_image) VALUES
('Daniel García',  'daniel@example.com',  '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar1.jpg'),
('Laura Pérez',    'laura@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar3.jpg'),
('Jorge Ruiz',     'jorge@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar2.jpg'),
('Sara López',     'sara@example.com',    '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar1.jpg'),
('Marc Torres',    'marc@example.com',    '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar2.jpg'),
('Paula Gómez',    'paula@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar3.jpg'),
('Iván Morales',   'ivan@example.com',    '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar1.jpg'),
('Claudia Díaz',   'claudia@example.com', '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar3.jpg'),
('Rubén Santos',   'ruben@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar2.jpg'),
('Nuria Beltrán',  'nuria@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar1.jpg'),
('Óscar Vidal',    'oscar@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar3.jpg'),
('Elena Martín',   'elena@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar2.jpg'),
('Hugo Serrano',   'hugo@example.com',    '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar1.jpg'),
('Lucía Ramos',    'lucia@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar2.jpg'),
('Adrián Soto',    'adrian@example.com',  '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar3.jpg'),
('Marta Cebrián',  'marta@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar1.jpg'),
('Sergio Peña',    'sergio@example.com',  '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar3.jpg'),
('Patricia Roldán','patricia@example.com','$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar2.jpg'),
('David Mena',     'david@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar1.jpg'),
('Irene Cortés',   'irene@example.com',   '$2b$10$ogHR6oA4F5F36HSgjxRv4udRxLbZQlaf8wcp6hiEN2bIWHoONxq1K', './assets/images/avatar3.jpg');





INSERT INTO plans (id, title, description, category, lat, lng, direccion, image, created_by, created_at) VALUES
(7,'Paseo por el Retiro','Caminar tranquilamente por el Parque de El Retiro durante 30-45 minutos.','Feliz',40.415363,-3.684401,'Parque de El Retiro, 28009 Madrid','./assets/images/imagenesPlanes/Parquedelretiro.jpg',1,'2025-12-04 00:39:57'),
(8,'Ir al cine en Príncipe Pío','Ver una película en una sala cercana.','Sorprendido',40.421259,-3.720927,'C.C. Príncipe Pío, Paseo de la Florida 2, 28008 Madrid','./assets/images/imagenesPlanes/cine.jpg',2,'2025-12-04 00:39:57'),
(9,'Cenar en La Latina','Salir a cenar de tapas o raciones en la zona de La Latina.','Enamorado',40.411365,-3.708574,'Calle Cava Baja, 28005 Madrid','./assets/images/imagenesPlanes/cenarLavapies.jpg',3,'2025-12-04 00:39:57'),
(10,'Correr por Casa de Campo','Hacer running suave alrededor del lago de Casa de Campo.','Enfadado',40.41378,-3.74572,'Lago de la Casa de Campo, 28011 Madrid','./assets/images/imagenesPlanes/correrporcasacampo.jpg',4,'2025-12-04 00:39:57'),
(11,'Tomar café en Malasaña','Quedar con alguien para tomar un café y charlar.','Feliz',40.42622,-3.70353,'Plaza del Dos de Mayo, 28004 Madrid','./assets/images/imagenesPlanes/tomarcafe.jpg',5,'2025-12-04 00:39:57'),
(12,'Ver una serie en casa','Elegir un capítulo pendiente y verlo sin móvil.','Triste',NULL,NULL,'Casa del usuario en Madrid o alrededores','./assets/images/imagenesPlanes/verserieencasa.jpg',6,'2025-12-04 00:39:57'),
(13,'Visitar un museo','Pasar la tarde viendo una exposición en el centro.','Sorprendido',40.41378,-3.692127,'Museo del Prado, Calle Ruiz de Alarcón 23, 28014 Madrid','./assets/images/imagenesPlanes/museo.jpg',7,'2025-12-04 00:39:57'),
(14,'Paseo al atardecer por Madrid Río','Caminar junto al río Manzanares al final del día.','Feliz',40.40153,-3.71835,'Madrid Río, Paseo de la Ermita del Santo, 28005 Madrid','./assets/images/imagenesPlanes/atardecer.jpg',8,'2025-12-04 00:39:57'),
(15,'Ir de compras a Gran Vía','Dar una vuelta por las tiendas de Gran Vía.','Feliz',40.42028,-3.70579,'Gran Vía, 28013 Madrid','./assets/images/imagenesPlanes/comprasgranvia.jpg',9,'2025-12-04 00:39:57'),
(16,'Leer en Biblioteca Nacional','Llevar un libro y leer en una sala tranquila.','Triste',40.4239,-3.69021,'Biblioteca Nacional, Paseo de Recoletos 20-22, 28001 Madrid','./assets/images/imagenesPlanes/biblioteca.jpg',10,'2025-12-04 00:39:57'),
(17,'Meditar 10 minutos en casa','Buscar un lugar silencioso y hacer respiraciones profundas.','Triste',NULL,NULL,'Casa del usuario en Madrid o alrededores','./assets/images/imagenesPlanes/meditar.jpg',11,'2025-12-04 00:39:57'),
(18,'Ir al gimnasio','Hacer una rutina sencilla de fuerza y cardio.','Enfadado',40.3375,-3.76028,'Gimnasio local en Alcorcón, Madrid','./assets/images/imagenesPlanes/gym.jpg',12,'2025-12-04 00:39:57'),
(19,'Cena en Las Rozas Village','Salir a cenar después de un paseo por el outlet.','Enamorado',40.515500,-3.888600,'Las Rozas Village, 28232 Las Rozas de Madrid','./assets/images/imagenesPlanes/cenaRomantica.jpg',13,'2025-12-04 00:39:57'),
(20,'Ver un partido en casa','Poner un partido de fútbol o baloncesto y relajarse.','Feliz',NULL,NULL,'Salón de casa con televisión','./assets/images/imagenesPlanes/Verfutbol.jpg',14,'2025-12-04 00:39:57'),
(21,'Caminar por el Parque de La Vaguada','Dar una vuelta tranquila por la zona verde.','Feliz',40.47839,-3.7089,'Parque de La Vaguada, 28029 Madrid','./assets/images/imagenesPlanes/paseo.jpg',15,'2025-12-04 00:39:57'),
(22,'Tomar helado en Moncloa','Salir a por un helado y pasear un poco.','Feliz',40.4352,-3.719,'Plaza de Moncloa, 28008 Madrid','./assets/images/imagenesPlanes/Comerhelado.jpg',16,'2025-12-04 00:39:57'),
(23,'Noche de lectura tranquila','Leer un par de capítulos de un libro pendiente.','Triste',NULL,NULL,'Casa del usuario','assets/images/imagenesPlanes/lecturaNoche.jpg',17,'2025-12-04 00:39:57'),
(24,'Ruta corta por Cercedilla','Escapada rápida a la sierra para una caminata sencilla.','Sorprendido',40.7416,-4.0566,'Cercedilla, 28470 Madrid','assets/images/imagenesPlanes/ruta.jpg',18,'2025-12-04 00:39:57'),
(25,'Cine en Cinesa Méndez Álvaro','Ver una película lo más interesante de la cartelera.','Sorprendido',40.3961,-3.6839,'C.C. Méndez Álvaro, Calle Retama 8, 28045 Madrid','assets/images/imagenesPlanes/cine.jpg',19,'2025-12-04 00:39:57'),
(26,'Cenar sushi en Pozuelo','Probar un menú de sushi sencillo.','Enamorado',40.4356,-3.8095,'Sushi en Pozuelo de Alarcón, 28223 Madrid','assets/images/imagenesPlanes/sushi.jpg',20,'2025-12-04 00:39:57'),
(27,'Paseo por Plaza Mayor y alrededores','Caminar por el centro histórico sin prisas.','Feliz',40.41536,-3.707398,'Plaza Mayor, 28012 Madrid','assets/images/imagenesPlanes/plazamayor.jpg',18,'2025-12-04 00:39:57'),
(30,'Sesión de música con cascos','Escuchar un álbum completo sin interrupciones.','Triste',NULL,NULL,'Habitación de casa','assets/images/imagenesPlanes/escucharMusica.jpg',19,'2025-12-04 00:39:57'),
(31,'Correr por Madrid Río','Realizar una carrera suave de 20-30 minutos.','Enfadado',40.3949,-3.7079,'Tramo de Madrid Río a la altura de Legazpi','assets/images/imagenesPlanes/footing.jpg',20,'2025-12-04 00:39:57');


INSERT INTO reviews (user_id, plan_id, rating) VALUES
(1,7,5),
(2,7,4),
(3,7,3),
(4,7,2),
(5,7,1),
(6,7,4),
(7,7,3),

(4,8,4),
(5,8,3),
(6,8,2),
(7,8,5),
(8,8,4),
(9,8,3),
(10,8,2),

(7,9,3),
(8,9,2),
(9,9,5),
(10,9,1),
(11,9,3),
(12,9,2),
(13,9,5),

(10,10,4),
(11,10,3),
(12,10,5),
(13,10,5),
(14,10,4),
(15,10,3),
(16,10,5),

(13,11,3),
(14,11,2),
(15,11,5),
(16,11,4),
(17,11,3),
(18,11,2),
(19,11,1),

(16,12,4),
(17,12,3),
(18,12,2),
(19,12,5),
(20,12,2),
(1,12,3),
(2,12,5),

(19,13,3),
(20,13,2),
(1,13,5),
(2,13,2),
(3,13,3),
(4,13,2),
(5,13,5),

(2,14,4),
(3,14,3),
(4,14,2),
(5,14,1),
(6,14,4),
(7,14,3),
(8,14,5),

(5,15,3),
(6,15,2),
(7,15,5),
(8,15,4),
(9,15,3),
(10,15,5),
(11,15,5),

(8,16,4),
(9,16,3),
(10,16,2),
(11,16,5),
(12,16,4),
(13,16,5),
(14,16,5),

(11,17,3),
(12,17,2),
(13,17,5),
(14,17,4),
(15,17,3),
(16,17,2),
(17,17,2),

(14,18,4),
(15,18,3),
(16,18,2),
(17,18,5),
(18,18,1),
(19,18,3),
(20,18,5),

(17,19,3),
(18,19,2),
(19,19,5),
(20,19,4),
(1,19,1),
(2,19,2),
(3,19,5),

(20,20,4),
(1,20,3),
(2,20,2),
(3,20,5),
(4,20,4),
(5,20,5),
(6,20,5),

(3,21,3),
(4,21,2),
(5,21,5),
(6,21,5),
(7,21,3),
(8,21,2),
(9,21,5),

(6,22,4),
(7,22,3),
(8,22,2),
(9,22,5),
(10,22,5),
(11,22,3),
(12,22,5),

(9,23,3),
(10,23,2),
(11,23,1),
(12,23,4),
(13,23,3),
(14,23,2),
(15,23,5),

(12,24,4),
(13,24,3),
(14,24,2),
(15,24,5),
(16,24,4),
(17,24,3),
(18,24,5),

(15,25,3),
(16,25,2),
(17,25,5),
(18,25,4),
(19,25,2),
(20,25,2),
(1,25,5),

(18,26,4),
(19,26,3),
(20,26,2),
(1,26,5),
(2,26,3),
(3,26,3),
(4,26,5),

(1,27,3),
(2,27,2),
(3,27,2),
(4,27,4),
(5,27,3),
(6,27,2),
(7,27,5),

(4,30,4),
(5,30,3),
(6,30,2),
(7,30,4),
(8,30,4),
(9,30,3),
(10,30,5),

(7,31,3),
(8,31,2),
(9,31,5),
(10,31,4),
(11,31,3),
(12,31,2),
(13,31,3);