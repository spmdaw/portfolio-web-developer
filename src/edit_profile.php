<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];

// ---------------- CONFIGURACIÓN DB ----------------
$host = "localhost";
$db   = "moodplanned";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión']);
    exit;
}

// ---------------- RUTA DE SUBIDA ----------------
// Asegúrate de que esta carpeta tenga permisos de escritura 
$uploadDir = __DIR__ . '/../public/assets/images/profiles/'; 
if (!file_exists($uploadDir)) mkdir($uploadDir, 0755, true);

$updates = [];
$params = [];

// ---------------- FUNCIONES ----------------
function validarImagen($file, $maxSizeMB = 10) {
    // 1. COMPROBAMOS QUE LOS CAMBIOS SEON VALIDOS PARA MODIFICAR EL PERFIL
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño permitido por el servidor.',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño permitido por el formulario.',
            UPLOAD_ERR_PARTIAL => 'El archivo solo se subió parcialmente.',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo.',
        ];
        return $uploadErrors[$file['error']] ?? 'Error desconocido al subir el archivo.';
    }

    // 2. Verificar tipo MIME
    $allowedTypes = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return 'Formato no permitido (solo JPG, PNG, WEBP, GIF)';
    }

    // 3. Verificar tamaño 
    if ($file['size'] > $maxSizeMB * 1024 * 1024) {
        return 'El archivo es demasiado grande (máx '.$maxSizeMB.'MB)';
    }

    // 4. Verificar si es realmente una imagen
    if (getimagesize($file['tmp_name']) === false) {
        return 'El archivo no es una imagen válida o está corrupto';
    }

    return true;
}

function subirImagen($file, $prefijo, $uploadDir) {
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    // Usamos uniqid para evitar problemas de caché en el navegador al cambiar la imagen
    $nombre = $prefijo . '_' . uniqid() . '.' . $ext;
    $rutaFisica = $uploadDir . $nombre;
    
    if (move_uploaded_file($file['tmp_name'], $rutaFisica)) {
        return '../public/assets/images/profiles/' . $nombre; 
    }
    return false;
}

// ---------------- PROCESAR DATOS ----------------

// 1. Bio
if (isset($_POST['bio'])) {
    $bio = trim($_POST['bio']);
    $updates[] = "bio = ?";
    $params[] = $bio;
}

// 2. Avatar
if (!empty($_FILES['avatar']['name'])) {
    $validacion = validarImagen($_FILES['avatar'], 5);
    if ($validacion !== true) {
        echo json_encode(['status' => 'error', 'message' => 'Avatar: ' . $validacion]);
        exit;
    }
    
    $rutaAvatar = subirImagen($_FILES['avatar'], 'avatar_'.$userId, $uploadDir);
    if ($rutaAvatar) {
        $updates[] = "profile_image = ?";
        $params[] = $rutaAvatar;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo de Avatar']);
        exit;
    }
}

// 3. Banner (AQUÍ ESTÁ LA LÓGICA DEL BANNER)
if (!empty($_FILES['banner']['name'])) {
    $validacion = validarImagen($_FILES['banner'], 10);
    if ($validacion !== true) {
        echo json_encode(['status' => 'error', 'message' => 'Banner: ' . $validacion]);
        exit;
    }

    $rutaBanner = subirImagen($_FILES['banner'], 'banner_'.$userId, $uploadDir);
    if ($rutaBanner) {
        $updates[] = "banner = ?";
        $params[] = $rutaBanner;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al mover el archivo del Banner']);
        exit;
    }
}

// ---------------- EJECUTAR SQL ----------------
if (!empty($updates)) {
    try {
        $params[] = $userId; // El ID va al final para el WHERE
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        echo json_encode(['status' => 'ok', 'message' => 'Perfil actualizado']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error DB: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'ok', 'message' => 'No hubo cambios']);
}
?>