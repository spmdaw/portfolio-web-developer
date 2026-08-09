<?php
// profile.php

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
    // Si no es establece la conexion se detiene la ejecucion del programa 
    die("Error de conexión a la Base de Datos: " . $e->getMessage());
}

// ---------------- INICIO DE SESIÓN ----------------
session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    if (isset($_GET['action'])) {
        die(json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']));
    }
}

// ---------------- FUNCIONES ----------------
// 
function fetchPlans(PDO $pdo, string $type, int $userId): array
{
    $baseQuery = "
        SELECT p.*, 
                (SELECT AVG(rating) FROM reviews WHERE plan_id = p.id) AS rating,
                EXISTS(SELECT 1 FROM favorites f2 WHERE f2.user_id = ? AND f2.plan_id = p.id) AS is_favorite,
                EXISTS(SELECT 1 FROM saved_plans s2 WHERE s2.user_id = ? AND s2.plan_id = p.id) AS is_saved
    ";

    $params = [$userId, $userId, $userId];

    switch ($type) {
        case 'favoritos':
            $sql = $baseQuery . " FROM favorites f
                                    JOIN plans p ON f.plan_id = p.id
                                    WHERE f.user_id = ?";
            break;
        case 'publicaciones':
            $sql = $baseQuery . " FROM plans p WHERE p.created_by = ?";
            break;
        case 'guardados':
            $sql = $baseQuery . " FROM saved_plans s
                                    JOIN plans p ON s.plan_id = p.id
                                    WHERE s.user_id = ?";
            break;
        default:
            return [];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$plan) {
        $plan['is_favorite'] = (bool)$plan['is_favorite'];
        $plan['is_saved'] = (bool)$plan['is_saved'];
    }

    return $data;
}

function simpleCount(PDO $pdo, string $table, string $column, int $userId, string $conditionColumn = null): int
{
    $sql = $conditionColumn
        ? "SELECT COUNT(*) AS total FROM $table WHERE $conditionColumn = ?"
        : "SELECT COUNT($column) AS total FROM $table WHERE $column = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// ---------------- API AJAX ----------------
if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');

    switch ($_GET['action']) {
        case 'list':
            $type = $_GET['type'] ?? 'favoritos';
            echo json_encode(['status' => 'ok', 'data' => fetchPlans($pdo, $type, $userId)], JSON_UNESCAPED_UNICODE);
            break;

        case 'count_created':
            echo json_encode(['status' => 'ok', 'count' => simpleCount($pdo, 'plans', '*', $userId, 'created_by')]);
            break;

        case 'count_completed':
            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT plan_id) AS total FROM reviews WHERE user_id = ?");
            $stmt->execute([$userId]);
            echo json_encode(['status' => 'ok', 'count' => (int)$stmt->fetch(PDO::FETCH_ASSOC)['total']]);
            break;

        case 'top_moods':
        case 'mood_stats':
            $limit = $_GET['action'] === 'top_moods' ? "LIMIT 3" : "";
            $stmt = $pdo->prepare("
                SELECT mood, COUNT(*) AS count
                FROM user_mood_tracker
                WHERE user_id = ?
                GROUP BY mood
                ORDER BY count DESC
                $limit
            ");
            $stmt->execute([$userId]);
            echo json_encode(['status' => 'ok', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)], JSON_UNESCAPED_UNICODE);
            break;

        case 'get_rating':
            $planId = $_GET['plan_id'] ?? 0;
            if ($planId) {
                // Obtener el nuevo rating promedio
                $stmt = $pdo->prepare("SELECT AVG(rating) AS avg_rating FROM reviews WHERE plan_id = ?");
                $stmt->execute([$planId]);
                $ratingData = $stmt->fetch(PDO::FETCH_ASSOC);
                // Redondeamos para que el formato sea el mismo que en el HTML
                $avgRating = round($ratingData['avg_rating'] ?? 0, 1);

                echo json_encode(['status' => 'ok', 'avg_rating' => $avgRating]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'ID de plan no proporcionado']);
            }
            break;
        // ----------------------------------------------------
        default:
            echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
    }
    exit;
}

// ---------------- DATOS DE USUARIO ----------------
// Recogemos los datos del usuario 
$stmt = $pdo->prepare("SELECT name, profile_image, banner, bio, points FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ---------------- PLAN MEJOR VALORADO ----------------
//Plan creado por el usuario mejor valorado por otros usuarios
$stmt = $pdo->prepare("
    SELECT p.title, AVG(r.rating) AS avg_rating
    FROM plans p
    LEFT JOIN reviews r ON p.id = r.plan_id
    WHERE p.created_by = ?
    GROUP BY p.id
    ORDER BY avg_rating DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$bestPlan = $stmt->fetch(PDO::FETCH_ASSOC);
// ---------------- CARGAR DATOS DE GALERÍA ----------------
// Usamos tu función fetchPlans para traer los arrays de datos
$misPublicaciones = fetchPlans($pdo, 'publicaciones', $userId);
$misFavoritos     = fetchPlans($pdo, 'favoritos', $userId);
$misGuardados     = fetchPlans($pdo, 'guardados', $userId);

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil de Usuario - MoodPlaned</title>

    <title>Moodplaned</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="./assets/css/profile.css" />
    <link rel="stylesheet" href="./assets/css/style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link href="assets/images/icon.jpg" rel="icon">
    <style>
        /* ==== MODAL DE PERFIL ==== */
        .modal-content {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        /* Header */
        .modal-header-profile {
            background: linear-gradient(135deg, #4f46e5, #6d28d9);
            color: #fff;
            padding: 1.2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header-profile .modal-title-profile {
            font-weight: 600;
            font-size: 1.25rem;
        }

        .modal-header-profile .btn-close {
            filter: invert(1);
            opacity: 0.9;
        }

        /* Cuerpo */
        .modal-body-profile {
            padding: 2rem 1.5rem;
            background-color: #fafafa;
        }

        .modal-body-profile label {
            font-weight: 600;
            color: #333;
        }

        .modal-body-profile input[type="file"],
        .modal-body-profile input[type="text"],
        .modal-body-profile textarea {
            border-radius: 10px;
            border: 1px solid #ddd;
            padding: 0.6rem 0.8rem;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .modal-body-profile input:focus,
        .modal-body-profile textarea:focus {
            border-color: #6d28d9;
            box-shadow: 0 0 0 0.15rem rgba(109, 40, 217, 0.25);
        }

        /* Vista previa de imágenes */
        #bannerPreview {
            border-radius: 12px;
            width: 100%;
            max-height: 200px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        #perfilPreview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #6d28d9;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        }

        /* Footer */
        .modal-footer-profile {
            display: flex;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            background-color: #f1f1f1;
        }

        .modal-footer-profile .btn {
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .modal-footer-profile .btn-primary {
            background-color: #6d28d9;
            border: none;
        }

        .modal-footer-profile .btn-primary:hover {
            background-color: #4f46e5;
        }

        .modal-footer-profile .btn-secondary {
            background-color: #e5e7eb;
            color: #333;
            border: none;
        }

        .modal-footer-profile .btn-secondary:hover {
            background-color: #d1d5db;
        }

        /* Animación al abrir el modal */
        .modal.fade .modal-dialog {
            transform: scale(0.95);
            transition: all 0.25s ease-in-out;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }



        .profile-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .profile-tab {
            padding: 8px 14px;
            border-radius: 10px;
            cursor: pointer;
            border: 1px solid #ccc;
        }

        .profile-tab.active {
            background: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        /* --- ESTILOS TIPO INSTAGRAM --- */
        .ig-nav {
            border-top: 1px solid #dbdbdb;
            display: flex;
            justify-content: center;
            gap: 60px;
            margin-top: 3rem;
            margin-bottom: 1rem;
        }

        .ig-tab-btn {
            background: none;
            border: none;
            border-top: 1px solid transparent;
            color: #8e8e8e;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            padding: 15px 0;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: 0.3s;
            margin-top: -1px;
            /* Para superponer el borde */
        }

        .ig-tab-btn.active {
            border-top: 1px solid #262626;
            color: #262626;
        }

        /* Grid de 3 columnas */
        .ig-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 28px;
            padding-bottom: 50px;
        }

        .ig-item {
            position: relative;
            cursor: pointer;
            aspect-ratio: 1 / 1;
            /* Mantiene la foto cuadrada */
            background-color: #efefef;
            overflow: hidden;
        }

        .ig-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }

        /* Efecto Hover */
        .ig-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            /* Invisible por defecto */
            transition: opacity 0.2s ease-in-out;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            gap: 15px;
        }

        .ig-item:hover .ig-overlay {
            opacity: 1;
        }

        .ig-item:hover .ig-image {
            transform: scale(1.05);
            /* Pequeño zoom al pasar el mouse */
        }

        /* Responsive para móviles */
        @media (max-width: 768px) {
            .ig-grid {
                gap: 3px;
            }

            .ig-nav {
                gap: 15px;
            }

            .ig-tab-btn span {
                display: none;
            }

            /* Solo iconos en móvil */
        }
    </style>

<body>

    <?php include 'include-header.php'; ?>
    <div class="profile-header" style="background-image: url('<?= !empty($user['banner']) ? htmlspecialchars($user['banner']) : './assets/images/defaultBanner.jpg' ?>'); background-size: cover; background-position: center;">
        <div class="profile-overlay">
            <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Foto de perfil">
        </div>
    </div>
    <div class="profile-info">
        <h3><?= htmlspecialchars($user['name'] ?? 'Usuario') ?></h3>
        <p>@<?= strtolower(htmlspecialchars($user['name'] ?? 'user')) ?> · <?= htmlspecialchars($user['bio'] ?? 'Amante de los viajes y las emociones') ?></p>

        <div class="mt-3">
            <button type="button" class="btn btn-edit-perfil me-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil-square"></i> Editar perfil
            </button>
            <a class="btn btn-outline-danger btn-logout-perfil" href="./logout.php">
                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
            </a>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header-profile">
                    <h5 class="modal-title-profile" id="editProfileModalLabel">Editar perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body-profile">
                    <form id="editProfileForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="bannerInput" class="form-label">Banner</label>
                            <input type="file" name="banner" id="bannerInput" class="form-control" accept="image/*">
                            <div class="mt-3">
                                <img src="<?= htmlspecialchars($user['banner'] ?? '') ?>" id="bannerPreview" style="width:100%; max-height:200px; object-fit:cover;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="avatarInput" class="form-label">Foto de perfil</label>
                            <input type="file" name="avatar" id="avatarInput" class="form-control" accept="image/*">
                            <div class="mt-3">
                                <img src="<?= htmlspecialchars($user['profile_image'] ?? '') ?>" id="avatarPreview" style="width:120px; height:120px; border-radius:50%; object-fit:cover; border:3px solid #6d28d9;">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bioInput" class="form-label">Descripción</label>
                            <textarea name="bio" id="bioInput" class="form-control" rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer-profile">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="guardarPerfil()">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Previsualizar Banner
        document.getElementById("bannerInput").addEventListener("change", function() {
            const file = this.files[0];
            if (file) {
                document.getElementById("bannerPreview").src = URL.createObjectURL(file);
            }
        });

        // Previsualizar Avatar
        document.getElementById("avatarInput").addEventListener("change", function() {
            const file = this.files[0];
            if (file) {
                document.getElementById("avatarPreview").src = URL.createObjectURL(file);
            }
        });
    </script>

    <main class="container mt-5 pt-5">

        <section class="mb-5">
            <div class="row g-3 justify-content-center text-center">
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="stats-card">
                        <h4 id="created-plans-count">0</h4>
                        <p>Planes creados</p>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="stats-card">
                        <h4 id="top-moods">...</h4>
                        <p>Emociones más vividas</p>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="stats-card">
                        <h4 id="completed-plans-count">0</h4>
                        <p>Planes realizados</p>
                    </div>
                </div>

                <div class="col-6 col-md-4 col-lg-3">
                    <div class="stats-card">
                        <h4><?= htmlspecialchars($bestPlan['title'] ?? '—') ?> (<?= round($bestPlan['avg_rating'] ?? 0, 1) ?>★)</h4>

                        <p>Plan mejor valorado</p>
                    </div>
                </div>

            </div>
            <div class="row mt-5">
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-6  d-flex justify-content-center">
                    <div class="stats-card mb-4 text-center" style="width: 90%; height: 20rem;">
                        <h5 class="m-0">Grafico de emociones</h5>
                        <canvas id="muscleChart"></canvas>
                    </div>
                </div>
                <div class="col-12 col-xs-12 col-sm-12 col-md-6 col-lg-6 d-flex justify-content-center">
                    <div class="stats-card mb-4 text-center" style="width: 90%; height: 20rem;">
                        <h5 class="m-0">Grafico de emociones</h5>
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="ig-nav">
                <button class="ig-tab-btn active" data-tab="tab-publicaciones" onclick="openIgTab(event, 'tab-publicaciones')">
                    <i class="fas fa-th"></i> <span>PUBLICACIONES</span>
                </button>
                <button class="ig-tab-btn" data-tab="tab-favoritos" onclick="openIgTab(event, 'tab-favoritos')">
                    <i class="far fa-heart"></i> <span>FAVORITOS</span>
                </button>
                <button class="ig-tab-btn" data-tab="tab-guardados" onclick="openIgTab(event, 'tab-guardados')">
                    <i class="far fa-bookmark"></i> <span>GUARDADOS</span>
                </button>
            </div>

            <div id="tab-publicaciones" class="ig-content" style="display: block;">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php if (empty($misPublicaciones)): ?>
                        <div class="col-12 text-center py-5">
                            <p class="text-muted">Aún no has creado ningún plan.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($misPublicaciones as $plan):
                            // 1. Preparar datos para inyectar en HTML/JS
                            $planJson = htmlspecialchars(json_encode($plan), ENT_QUOTES, 'UTF-8');
                            $rating = round($plan['rating'] ?? 0, 1);
                            $image = htmlspecialchars($plan['image'] ?? 'https://via.placeholder.com/500');
                            $parts = explode(',', $plan['direccion']);
                            $ciudadConCodigo = count($parts) >= 2 ? trim($parts[count($parts) - 2]) : $plan['direccion'];
                            $ciudad = preg_replace('/^\d+\s+/', '', $ciudadConCodigo);

                            $is_favorite_class = ($plan['is_favorite'] ?? false) ? 'bi-heart-fill text-danger' : 'bi-heart text-danger';
                            $is_saved_class = ($plan['is_saved'] ?? false) ? 'bi-bookmark-fill text-primary' : 'bi-bookmark text-primary';
                        ?>
                            <div class="col">
                                <div class="card plan-card-perfil shadow-sm"
                                    onclick="openPlanModal(<?= $planJson ?>, true)">

                                    <div class="position-relative">
                                        <img src="<?= $image ?>" class="card-img-top" alt="<?= htmlspecialchars($plan['title'] ?? 'Plan sin título') ?>">

                                        <div class="rating-badge"><i class="bi bi-star-fill"></i> <?= $rating ?></div>

                                        <div class="card-overlay-perfil">
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($plan['title'] ?? 'Sin título') ?></h5>
                                            <small><?= $ciudad ?></small>
                                        </div>

                                        <div class="card-icons position-absolute top-2 end-2 d-flex gap-2">
                                            <i class="bi <?= $is_favorite_class ?> favorite-icon" data-plan-id="<?= htmlspecialchars($plan['id']) ?>"></i>
                                            <i class="bi <?= $is_saved_class ?> save-icon" data-plan-id="<?= htmlspecialchars($plan['id']) ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-favoritos" class="ig-content" style="display: none;">
                <div class="ig-grid">
                    <?php if (empty($misFavoritos)): ?>
                        <div class="col-12 text-center py-5" style="grid-column: 1/-1;">
                            <p class="text-muted">No tienes favoritos aún.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($misFavoritos as $plan):
                            $planJson = htmlspecialchars(json_encode($plan), ENT_QUOTES, 'UTF-8');
                            $rating = round($plan['rating'] ?? 0, 1);
                            $image = htmlspecialchars($plan['image'] ?? 'https://via.placeholder.com/500');
                            $parts = explode(',', $plan['direccion']);
                            $ciudadConCodigo = count($parts) >= 2 ? trim($parts[count($parts) - 2]) : $plan['direccion'];
                            $ciudad = preg_replace('/^\d+\s+/', '', $ciudadConCodigo);


                            $is_favorite_class = ($plan['is_favorite'] ?? false) ? 'bi-heart-fill text-danger' : 'bi-heart text-danger';
                            $is_saved_class = ($plan['is_saved'] ?? false) ? 'bi-bookmark-fill text-primary' : 'bi-bookmark text-primary';
                        ?>
                            <div class="col">
                                <div class="card plan-card-perfil shadow-sm"
                                    onclick="openPlanModal(<?= $planJson ?>, false)">

                                    <div class="position-relative">
                                        <img src="<?= $image ?>" class="card-img-top" alt="<?= htmlspecialchars($plan['title'] ?? 'Plan sin título') ?>">

                                        <div class="rating-badge"><i class="bi bi-star-fill"></i> <?= $rating ?></div>

                                        <div class="card-overlay-perfil">
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($plan['title'] ?? 'Sin título') ?></h5>
                                            <small><?= $ciudad ?></small>
                                        </div>

                                        <div class="card-icons position-absolute top-2 end-2 d-flex gap-2">
                                            <i class="bi <?= $is_favorite_class ?> favorite-icon" data-plan-id="<?= htmlspecialchars($plan['id']) ?>"></i>
                                            <i class="bi <?= $is_saved_class ?> save-icon" data-plan-id="<?= htmlspecialchars($plan['id']) ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-guardados" class="ig-content" style="display: none;">
                <div class="ig-grid">
                    <?php if (empty($misGuardados)): ?>
                        <div class="col-12 text-center py-5" style="grid-column: 1/-1;">
                            <p class="text-muted">No tienes planes guardados.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($misGuardados as $plan):
                            $planJson = htmlspecialchars(json_encode($plan), ENT_QUOTES, 'UTF-8');
                            $rating = round($plan['rating'] ?? 0, 1);
                            $image = htmlspecialchars($plan['image'] ?? 'https://via.placeholder.com/500');
                            $parts = explode(',', $plan['direccion']);
                            $ciudadConCodigo = count($parts) >= 2 ? trim($parts[count($parts) - 2]) : $plan['direccion'];
                            $ciudad = preg_replace('/^\d+\s+/', '', $ciudadConCodigo);

                            $is_favorite_class = ($plan['is_favorite'] ?? false) ? 'bi-heart-fill text-danger' : 'bi-heart text-danger';
                            $is_saved_class = ($plan['is_saved'] ?? false) ? 'bi-bookmark-fill text-primary' : 'bi-bookmark text-primary';
                        ?>
                            <div class="col">
                                <div class="card plan-card-perfil shadow-sm"
                                    onclick="openPlanModal(<?= $planJson ?>, false)">

                                    <div class="position-relative">
                                        <img src="<?= $image ?>" class="card-img-top" alt="<?= htmlspecialchars($plan['title'] ?? 'Plan sin título') ?>">

                                        <div class="rating-badge"><i class="bi bi-star-fill"></i> <?= $rating ?></div>

                                        <div class="card-overlay-perfil">
                                            <h5 class="card-title mb-1"><?= htmlspecialchars($plan['title'] ?? 'Sin título') ?></h5>
                                            <small><?= $ciudad ?></small>
                                        </div>

                                        <div class="card-icons position-absolute top-2 end-2 d-flex gap-2">
                                            <i class="bi <?= $is_favorite_class ?> favorite-icon" data-plan-id="<?= htmlspecialchars($plan['id']) ?>"></i>
                                            <i class="bi <?= $is_saved_class ?> save-icon" data-plan-id="<?= htmlspecialchars($plan['id']) ?>"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <div class="modal fade" id="planModal" tabindex="-1" aria-labelledby="planModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-dimensiones">
                <div class="modal-content border-0 rounded-4 overflow-hidden">
                    <div class="modal-img-container">
                        <img id="planModalImage" class="modal-img" src="https://via.placeholder.com/800x400" alt="plan">
                    </div>

                    <div class="modal-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="fw-bold mb-0" id="planModalTitle">Título del Plan</h3>
                            <div class="d-flex gap-2">
                                <button class="btn btn-light border rounded-circle p-2 favorite-btn" data-plan-id="">
                                    <i class="bi bi-heart text-danger"></i>
                                </button>

                                <button class="btn btn-light border rounded-circle p-2 save-btn" data-plan-id="">
                                    <i class="bi bi-bookmark text-primary"></i>
                                </button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center text-muted mb-3">
                            <i class="bi bi-geo-alt me-2"></i> <span id="planModalCategory">Categoría</span>
                        </div>
                        <p class="text-secondary mb-4" id="planModalDescription">Descripción del plan.</p>
                        <div class="d-flex justify-content-start">
                            <button class="btn btn-outline-primary px-4 me-2 edit-btn" data-plan-id="" type="button">Editar</button>
                            <button class="btn btn-outline-danger delete-btn" data-plan-id="" type="button">Eliminar</button>
                            <button class="btn btn-outline-success score-btn" data-plan-id="" type="button">Puntuar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="editPlanModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg p-3">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="editPlanModalLabel">Editar Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editPlanForm">
                            <input type="hidden" name="plan_id" id="edit-plan-id">
                            <div class="mb-3 form-group smooth">
                                <label for="edit-title" class="form-label">Título</label>
                                <input type="text" class="form-control input-field" id="edit-title" name="title" required>
                            </div>
                            <div class="mb-3 form-group smooth">
                                <label for="edit-description" class="form-label">Descripción</label>
                                <textarea class="form-control input-field textarea" id="edit-description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3 form-group smooth">
                                <label for="edit-category" class="form-label">Categoría</label>
                                <select name="category" class="input-field select" id="edit-category" required>
                                    <option>Feliz</option>
                                    <option>Triste</option>
                                    <option>Enfadado</option>
                                    <option>Sorprendido</option>
                                    <option>Enamorado</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-danger me-2" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn-submit">Guardar cambios</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de puntuación -->
        <div class="modal fade" id="scoreModal" tabindex="-1" aria-labelledby="scoreModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg p-3" style="background-color: rgba(232, 216, 216, 0.963);">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold" id="scoreModalLabel">Puntuar Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p class="mb-3">Selecciona tu puntuación:</p>
                        <div id="star-container" class="d-flex justify-content-center gap-2">
                            <i class="bi bi-star fs-2 star" data-value="1"></i>
                            <i class="bi bi-star fs-2 star" data-value="2"></i>
                            <i class="bi bi-star fs-2 star" data-value="3"></i>
                            <i class="bi bi-star fs-2 star" data-value="4"></i>
                            <i class="bi bi-star fs-2 star" data-value="5"></i>
                        </div>
                        <input type="hidden" id="score-plan-id" value="0">
                        <input type="hidden" id="selected-rating" value="0">
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" id="submitRating" class="btn-submit">Enviar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const currentUserId = <?= $_SESSION['user_id'] ?? 0 ?>;
            const moodEmojisGrafico = {
                'feliz': "😊",
                'triste': "😢",
                'enfadado': "😡",
                'sorprendido': "😲",
                'enamorado': "😍"
            };
            let currentOpenedPlan = null;


            let radarChart = null;
            let barChart = null;

            async function loadMoodStats() {
                try {
                    const res = await fetch('profile.php?action=mood_stats', {
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    if (json.status !== 'ok') return;

                    // Convertimos respuesta a un mapa
                    const countsMap = {};
                    json.data.forEach(m => countsMap[m.mood.toLowerCase()] = Number(m.count));

                    // Ahora generamos las etiquetas a partir del objeto emoji
                    const moodKeys = Object.keys(moodEmojisGrafico);
                    const labels = moodKeys.map(k => moodEmojisGrafico[k] + ' ' + k.charAt(0).toUpperCase() + k.slice(1));

                    //  generamos las estadísticas
                    const dataCounts = moodKeys.map(k => countsMap[k] || 0);

                    if (!radarChart) {
                        const ctx = document.getElementById('muscleChart');
                        if (!ctx) return;
                        radarChart = new Chart(ctx, {
                            type: 'radar',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Emociones',
                                    data: dataCounts,
                                    fill: true,
                                    backgroundColor: 'rgba(0,123,255,0.2)',
                                    borderColor: '#007bff',
                                    pointBackgroundColor: '#007bff',
                                }]
                            },
                            options: {
                                scales: {
                                    r: {
                                        ticks: {
                                            beginAtZero: true,
                                            max: Math.max(...dataCounts, 1)
                                        }
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    } else {
                        radarChart.data.labels = labels;
                        radarChart.data.datasets[0].data = dataCounts;
                        radarChart.options.scales.r.ticks.max = Math.max(...dataCounts, 1);
                        radarChart.update();
                    }

                    // === Bar Chart ===
                    if (!barChart) {
                        const ctx2 = document.getElementById('barChart');
                        if (!ctx2) return;
                        barChart = new Chart(ctx2, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'Emociones',
                                    data: dataCounts,
                                    backgroundColor: 'rgba(0,123,255,0.2)',
                                    borderColor: '#007bff',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: Math.max(...dataCounts, 1)
                                    }
                                },
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                }
                            }
                        });
                    } else {
                        barChart.data.labels = labels;
                        barChart.data.datasets[0].data = dataCounts;
                        barChart.options.scales.y.max = Math.max(...dataCounts, 1);
                        barChart.update();
                    }

                } catch (err) {
                    console.error('Error cargando estadísticas de emociones:', err);
                }
            }







            // TABS TIPO INSTAGRAM
            function tabIsActive(tabName) {
                const tab = document.getElementById(tabName);
                return tab && tab.style.display !== 'none';
            }

            function openIgTab(evt, tabName) {
                // 1. Ocultar todos los contenidos de la galería
                const tabcontent = document.getElementsByClassName("ig-content");
                for (let i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }

                // 2. Remover la clase 'active' de todos los botones de la galería
                const tablinks = document.getElementsByClassName("ig-tab-btn");
                for (let i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }

                // 3. Mostrar el contenido de la pestaña actual 
                document.getElementById(tabName).style.display = "block";
                evt.currentTarget.className += " active";
            }

            // MODALES Y EDICIÓN DE PLANES

            /**
             * Abre el modal de plan y carga los datos.
             */
            function openPlanModal(plan, isOwner) {
                currentOpenedPlan = plan;

                const iconos_category = {
                    'Feliz': '😊',
                    'Triste': '😢',
                    'Enfadado': '😡',
                    'Sorprendido': '😲',
                    'Enamorado': '😍'
                };

                const emoji = iconos_category[plan.category] ?? '🏷️';
                const modalEl = document.getElementById('planModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

                // 1. Cargar datos del modal
                modalEl.querySelector('#planModalImage').src = plan.image ?? 'https://via.placeholder.com/600';
                modalEl.querySelector('#planModalTitle').textContent = plan.title;

                const locationEl = modalEl.querySelector('.bi-geo-alt').parentNode;
                locationEl.innerHTML = `<i class="bi bi-geo-alt me-2 text-primary"></i> ${plan.direccion}${emoji}`;

                modalEl.querySelector('p.text-secondary').textContent = plan.description;

                // 2. BOTONES INDIVIDUALES
                const editBtn = modalEl.querySelector('.edit-btn');
                const deleteBtn = modalEl.querySelector('.delete-btn');
                const scoreBtn = modalEl.querySelector('.score-btn');

                // ──────────────────────────────────────────────
                // SI EL PLAN ES DEL USUARIO
                // ──────────────────────────────────────────────
                // El plan es tuyo si coincide el ID del creador con el usuario actual
                const realOwner = (plan.created_by == currentUserId);

                if (realOwner) {
                    editBtn.style.display = "inline-block";
                    deleteBtn.style.display = "inline-block";
                    scoreBtn.style.display = "none";

                    editBtn.onclick = () => {
                        modal.hide();
                        openEditPlanModal(plan);
                    };

                    deleteBtn.onclick = () => {
                        if (confirm(`¿Estás seguro de que deseas eliminar esta publicación?`)) {
                            deletePlan(plan.id, modal);
                        }
                    };
                }

                // ──────────────────────────────────────────────
                // SI EL PLAN NO ES DEL USUARIO
                // ──────────────────────────────────────────────
                else { // [cite: 1191]
                    editBtn.style.display = "none";
                    deleteBtn.style.display = "none";
                    scoreBtn.style.display = "inline-block";
                    scoreBtn.onclick = () => {
                        // AGREGAR ESTA LÍNEA PARA CERRAR EL MODAL DE PLANES
                        modal.hide();

                        const scoreModalEl = document.getElementById('scoreModal');
                        const scoreModal = bootstrap.Modal.getOrCreateInstance(scoreModalEl);

                        // Guardar el ID del plan en el modal de puntuación
                        document.getElementById('score-plan-id').value = plan.id;
                        scoreModal.show();
                    };
                }

                // 3. Favorito y Guardado
                const favoriteBtn = modalEl.querySelector('.favorite-btn');
                const saveBtn = modalEl.querySelector('.save-btn');

                favoriteBtn.dataset.planId = plan.id;
                saveBtn.dataset.planId = plan.id;

                const favoriteIcon = favoriteBtn.querySelector('i');
                const saveIcon = saveBtn.querySelector('i');

                favoriteIcon.className = `bi ${plan.is_favorite ? 'bi-heart-fill' : 'bi-heart'} text-danger`;
                saveIcon.className = `bi ${plan.is_saved ? 'bi-bookmark-fill' : 'bi-bookmark'} text-primary`;

                favoriteBtn.onclick = () => toggleIcon(favoriteIcon, 'favorite');
                saveBtn.onclick = () => toggleIcon(saveIcon, 'saved');

                modal.show();
            }



            /**
             * Abre el modal de edición de plan y precarga los campos.
             * @param {object} plan - Objeto plan a editar.
             */
            function openEditPlanModal(plan) {
                const modalEl = document.getElementById('editPlanModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

                // Precarga de datos en el formulario
                document.getElementById('edit-plan-id').value = plan.id;
                document.getElementById('edit-title').value = plan.title;
                document.getElementById('edit-description').value = plan.description;

                // Seleccionar la categoría actual
                const categorySelect = document.getElementById('edit-category');
                categorySelect.value = plan.category; // Asume que la columna es 'category'

                // Lógica para enviar el formulario de edición
                document.getElementById('editPlanForm').onsubmit = (e) => {
                    e.preventDefault();
                    editPlan(new FormData(e.target), modal);
                };

                modal.show();
            }

            /**
             * Actualiza en el DOM la card del plan que se acaba de editar.
             * formData: FormData con plan_id, title, description, category ...
             */
            function actualizarCardDelPlan(formData, json = null) {
                const id = formData.get('plan_id') || (json && json.plan_id) || (json && json.plan && json.plan.id);
                if (!id) return;

                // 1) Buscar la card a partir de los iconos (tu HTML los tiene)
                const icon = document.querySelector(`.favorite-icon[data-plan-id="${id}"], .save-icon[data-plan-id="${id}"]`);
                const card = icon ? icon.closest('.plan-card-perfil') : null;
                if (!card) {
                    console.warn('No se encontró la card del plan con id', id);
                    return;
                }

                // 2) Obtener nuevos valores
                const newTitle = (json && json.plan && json.plan.title) || formData.get('title') || null;
                const newDescription = (json && json.plan && json.plan.description) || formData.get('description') || null;
                const newCategory = (json && json.plan && (json.plan.category || json.plan.mood)) || formData.get('category') || null;

                // 3) Actualizar título en la card
                const titleEl = card.querySelector('.card-title');
                if (titleEl && newTitle !== null) titleEl.textContent = newTitle;

                // 4) Si guardaste descripción en data-* del card, actualízala
                // En tu perfilAntiguo usabas card.dataset.description; hacemos lo mismo si existe
                if (newDescription !== null) {
                    card.dataset.description = newDescription;
                }

                // 5) Si tienes un pequeño elemento con la ciudad / subtitulo, no tocar a menos que quieras
                // 6) Si fuera necesario actualizar iconos (favorito/guardado), puedes usar json.status o json.is_favorite
                if (json && json.is_favorite !== undefined) {
                    const favIcon = card.querySelector('.favorite-icon[data-plan-id="' + id + '"]');
                    if (favIcon) {
                        favIcon.className = 'bi ' + (json.is_favorite ? 'bi-heart-fill text-danger' : 'bi-heart text-danger') + ' favorite-icon';
                    }
                }

                // 7) PARA HACER QUE openPlanModal USE LOS DATOS ACTUALIZADOS:
                // Si tu card tiene onclick="openPlanModal(<planJson>, ...)", esa JSON embebida queda obsoleta.
                // Mejor: sobrescribimos el onclick para que al abrir el modal lea los datos actualizados del DOM.
                card.onclick = function() {
                    // Reconstruimos un objeto plan mínimo desde el DOM/dataset
                    const planObj = {
                        id: id,
                        title: newTitle || (titleEl ? titleEl.textContent : ''),
                        description: newDescription || card.dataset.description || '',
                        category: newCategory || card.dataset.category || '',
                        direccion: currentOpenedPlan.direccion,
                        image: card.querySelector('img.card-img-top') ? card.querySelector('img.card-img-top').src : ''
                    };
                    // true asumiendo que el usuario es owner aquí. Si no, ajusta.
                    openPlanModal(planObj, true);
                };

                // Si quieres, puedes destacar la card temporalmente para que el usuario note el cambio
                card.style.transition = "box-shadow 0.3s ease";
                card.style.boxShadow = "0 0 0 3px rgba(109,40,217,0.12)";
                setTimeout(() => card.style.boxShadow = "", 1200);
            }

            /**
             * Función para actualizar el rating en la card y el contador de planes realizados sin recargar.
             * @param {number} planId - ID del plan.
             */
            async function updatePlanUI(planId) {
                try {
                    // 1. Obtener el rating actualizado del servidor
                    const res = await fetch(`profile.php?action=get_rating&plan_id=${planId}`, {
                        credentials: 'same-origin'
                    });
                    const json = await res.json();

                    if (json.status !== 'ok') {
                        throw new Error(json.message || 'Error al obtener nuevo rating');
                    }

                    const newRating = parseFloat(json.avg_rating);
                    const formattedRating = newRating.toFixed(1);

                    // 2. Buscar y actualizar todas las cards con ese planId
                    // Usamos el data-plan-id de los iconos para encontrar la card padre
                    document.querySelectorAll(`.plan-card-perfil [data-plan-id="${planId}"]`).forEach(icon => {
                        const card = icon.closest('.col'); // La card del plan está dentro de un div.col
                        if (card) {
                            // Actualizar el badge de rating
                            const ratingBadge = card.querySelector('.rating-badge');
                            if (ratingBadge) {
                                ratingBadge.innerHTML = `<i class="bi bi-star-fill"></i> ${formattedRating}`;
                            }
                        }
                    });

                    // 3. Re-cargar los contadores de planes realizados (si fue la primera puntuación).
                    updateCompletedPlansCount();

                } catch (err) {
                    console.error('Error al actualizar la UI del plan:', err);
                }
            }

            // --------------------------------------------------------------------------------
            // PETICIONES AJAX PARA PLANES (CRUD)
            // --------------------------------------------------------------------------------

            /**
             * Maneja la eliminación de un plan.
             * @param {number} planId - ID del plan a eliminar.
             * @param {bootstrap.Modal} modalInstance - Instancia del modal de visualización para cerrarlo.
             */
            async function deletePlan(planId, modalInstance) {
                try {
                    const res = await fetch('../src/delete_plan.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `plan_id=${planId}`,
                        credentials: 'same-origin'
                    });

                    const json = await res.json();

                    if (json.success !== true) {
                        alert('Error al eliminar el plan: ' + (json.message || 'Error desconocido'));
                        return;
                    }
                    modalInstance.hide();
                    const card = document.querySelector(
                        `.favorite-icon[data-plan-id="${planId}"], .save-icon[data-plan-id="${planId}"]`)?.closest('.col');
                    if (card) {
                        card.style.transition = "opacity .3s";
                        card.style.opacity = 0;

                        setTimeout(() => card.remove(), 300);
                    }
                    currentOpenedPlan = null;

                } catch (err) {
                    console.error('Error al eliminar el plan:', err);
                    alert('Error de conexión al eliminar el plan.');
                }
            }


            /**
             * Maneja la edición de un plan.
             * @param {FormData} formData - Datos del formulario de edición.
             * @param {bootstrap.Modal} modalInstance - Instancia del modal de edición para cerrarlo.
             */
            async function editPlan(formData, modalInstance) {
                try {
                    const res = await fetch('../src/edit_plan.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });

                    // Leemos el JSON
                    const contentType = res.headers.get("content-type") || "";
                    let json;
                    if (contentType.includes("application/json")) {
                        json = await res.json();
                    } else {
                        const text = await res.text();
                        throw new Error("Respuesta no JSON: " + text.substring(0, 200));
                    }

                    // Alinea con tu estructura actual: acepta 'status' o 'success'
                    const ok = (json.status === 'ok') || (json.success === true);
                    if (!ok) {
                        alert(json.message || 'Error desconocido al actualizar el plan');
                        return;
                    }

                    // 1) Cerrar modal de manera segura (siempre obtener/crear instancia)
                    const editModalEl = document.getElementById('editPlanModal');
                    const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                    editModal.hide();

                    // Limpieza extra (por si queda backdrop)
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style = '';

                    // 2) Actualizar la card en el DOM sin recargar
                    actualizarCardDelPlan(formData, json); // ver función más abajo

                } catch (err) {
                    console.error('Error al editar plan:', err);
                    alert('Error de conexión o respuesta inválida. Revisa la consola.');
                }
            }


            // --------------------------------------------------------------------------------
            // PETICIONES AJAX PARA ESTADÍSTICAS Y CONTADORES
            // --------------------------------------------------------------------------------

            async function updateCreatedPlansCount() {
                try {
                    const res = await fetch('profile.php?action=count_created', {
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    if (json.status === 'ok') {
                        document.getElementById('created-plans-count').textContent = json.count;
                    }
                } catch (err) {
                    console.error('Error cargando planes creados:', err);
                }
            }

            async function updateCompletedPlansCount() {
                try {
                    const res = await fetch('profile.php?action=count_completed', {
                        credentials: 'same-origin'
                    });
                    const json = await res.json();
                    if (json.status === 'ok') {
                        document.getElementById('completed-plans-count').textContent = json.count;
                    }
                } catch (err) {
                    console.error('Error cargando planes realizados:', err);
                }
            }

            async function updateTopMoods() {
                try {
                    const res = await fetch('profile.php?action=top_moods', {
                        credentials: 'same-origin'
                    });
                    const json = await res.json();

                    if (json.status === 'ok' && json.data.length > 0) {

                        const moods = json.data
                            .map(m => moodEmojisGrafico[m.mood.toLowerCase()] || '')
                            .join(' ');

                        document.getElementById('top-moods').innerHTML = moods;

                    } else {
                        document.getElementById('top-moods').innerHTML = '—';
                    }

                } catch (err) {
                    console.error('Error cargando top moods:', err);
                }
            }


            // Función de guardar perfil (pendiente de endpoint update_profile.php)
            async function guardarPerfil() {
                const form = document.getElementById('editProfileForm');
                const formData = new FormData(form);
                const modalEl = document.getElementById('editProfileModal');

                // Usamos getOrCreateInstance para evitar errores si la instancia se perdió
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

                try {
                    // Petición al backend que creamos (edit_profile.php)
                    const res = await fetch('../src/edit_profile.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });

                    const json = await res.json();
                    if (json.status !== 'ok') {
                        alert('Error: ' + json.message);
                        return;
                    }

                    // 1. Actualizar Avatar en la interfaz
                    const avatarFile = form.avatar.files[0];
                    if (avatarFile) {
                        document.querySelector('.profile-overlay img').src = URL.createObjectURL(avatarFile);
                    }

                    // 2. Actualizar Banner en la interfaz (NUEVO)
                    const bannerFile = form.banner.files[0];
                    if (bannerFile) {
                        const bannerURL = URL.createObjectURL(bannerFile);

                        // Actualiza la previsualización del modal
                        const bannerPreview = document.getElementById('bannerPreview');
                        if (bannerPreview) bannerPreview.src = bannerURL;

                        // Actualiza el fondo de la cabecera principal inmediatamente
                        const headerProfile = document.querySelector('.profile-header');
                        if (headerProfile) {
                            headerProfile.style.backgroundImage = `url('${bannerURL}')`;
                        }
                    }

                    // 3. Actualizar Biografía
                    const bioEl = document.querySelector('.profile-info p');

                    if (bioEl && form.bio.value) {
                        // Recuperamos el usuario actual del texto para no borrarlo
                        const currentText = bioEl.textContent;
                        const atIndex = currentText.indexOf('·');
                        if (atIndex !== -1) {
                            const userPrefix = currentText.substring(0, atIndex + 1);
                            bioEl.textContent = `${userPrefix} ${form.bio.value}`;
                        }
                    }

                    // Cerrar modal
                    modalEl.addEventListener('hidden.bs.modal', () => {
                        const triggerBtn = document.querySelector('.btn-edit-perfil');
                        if (triggerBtn) triggerBtn.focus();

                        // Limpieza extra por si Bootstrap deja basura
                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style = '';
                    }, {
                        once: true
                    });

                    modal.hide();

                } catch (err) {
                    console.error(err);
                    alert('Error al actualizar el perfil');
                }
            }



            async function toggleIcon(iconElement, type) {
                const planId = iconElement.closest('button, i').dataset.planId ||
                    iconElement.parentElement.dataset.planId ||
                    iconElement.dataset.planId;

                const isFilled = iconElement.classList.contains('bi-heart-fill') || iconElement.classList.contains('bi-bookmark-fill');

                let endpoint, filledClass, emptyClass;

                if (type === 'favorite') {
                    endpoint = '../src/toggle_favorite.php';
                    filledClass = 'bi-heart-fill text-danger';
                    emptyClass = 'bi-heart text-danger';
                } else if (type === 'saved') {
                    endpoint = '../src/toggle_saved.php';
                    filledClass = 'bi-bookmark-fill text-primary';
                    emptyClass = 'bi-bookmark text-primary';
                } else {
                    return;
                }

                // 1. Guardar estado actual y hacer cambio optimista
                const originalClass = iconElement.className;
                iconElement.className = `bi ${isFilled ? emptyClass : filledClass}`;

                // 2. Llamada AJAX
                try {
                    const res = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `plan_id=${planId}`,
                        credentials: 'same-origin'
                    });

                    // Nueva comprobación para errores HTTP (404, 500, etc.)
                    if (!res.ok) {
                        // Si da 500, el problema es la conexión DB en el PHP o un error fatal de código.
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }

                    // Comprobación de que la respuesta sea JSON, no HTML
                    const contentType = res.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        const text = await res.text();
                        throw new Error(`Expected JSON, received: ${text.substring(0, 50)}...`);
                    }

                    const json = await res.json();

                    if (json.success !== true) {
                        // Revertir si hubo error
                        iconElement.className = originalClass;
                        alert(`Error al actualizar ${type}: ` + (json.message || 'Error desconocido'));
                    }
                } catch (err) {
                    // Revertir en caso de error de conexión o fallo de HTTP
                    iconElement.className = originalClass;
                    console.error(`Error de conexión al actualizar ${type}:`, err);

                    let message = `Error de actualización. Causas: Ruta Incorrecta, Sesión no válida, o **FALLO DB (500)**. Mensaje: ${err.message}`;
                    if (err.message.includes("500")) {
                        message += " 🚨 **Verifique que su servidor MySQL/MariaDB esté activo.**";
                    }

                    alert(message);
                }
            }

            // Manejo de clic en estrellas
            document.querySelectorAll('#star-container .star').forEach(star => {
                star.addEventListener('click', function() {
                    const value = parseInt(this.dataset.value);
                    document.getElementById('selected-rating').value = value; // ✔ CORRECTO

                    // Pintar estrellas
                    document.querySelectorAll('#star-container .star').forEach(s => {
                        const v = parseInt(s.dataset.value);
                        if (v <= value) {
                            s.classList.remove('bi-star');
                            s.classList.add('bi-star-fill', 'text-warning');
                        } else {
                            s.classList.remove('bi-star-fill', 'text-warning');
                            s.classList.add('bi-star');
                        }
                    });
                });
            });

            // Enviar puntuación
            document.getElementById('submitRating').addEventListener('click', async function() {
                const planId = document.getElementById('score-plan-id').value;
                const rating = document.getElementById('selected-rating').value;
                if (rating == 0 || planId == 0) {
                    alert('Por favor, selecciona una puntuación.');
                    return;
                }
                // Llamada AJAX a review_plan.php
                fetch('../src/submit_rating.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `plan_id=${planId}&rating=${rating}`,
                        credentials: 'same-origin'
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'ok' || data.success === true) {
                            const modalEl = document.getElementById('scoreModal');
                            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            modal.hide();
                            updatePlanUI(planId);
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(err => console.error(err));
            });




            // INICIALIZACIÓN Y LISTENERS
            document.addEventListener('DOMContentLoaded', () => {
                // Cargar estadísticas iniciales
                updateCreatedPlansCount();
                updateCompletedPlansCount();
                updateTopMoods();
                loadMoodStats();

                // Refrescar cada 10s
                setInterval(() => {
                    updateCreatedPlansCount();
                    updateCompletedPlansCount();
                    updateTopMoods();
                    loadMoodStats();
                }, 10000);

                // 1. Agregar listeners a los iconos de Favoritos y Guardados de la galería
                document.querySelectorAll('.favorite-icon').forEach(icon => {
                    icon.addEventListener('click', (e) => {
                        e.stopPropagation(); // Evita que el click en el ícono abra el modal del plan
                        toggleIcon(e.currentTarget, 'favorite');
                    });
                });

                document.querySelectorAll('.save-icon').forEach(icon => {
                    icon.addEventListener('click', (e) => {
                        e.stopPropagation(); // Evita que el click en el ícono abra el modal del plan
                        toggleIcon(e.currentTarget, 'saved');
                    });
                });

                // 2. Asegurarse de que las funciones estén en el scope global
                window.openIgTab = openIgTab;
                window.openPlanModal = openPlanModal;
                window.guardarPerfil = guardarPerfil;
            });
        </script>
    </main>

    <?php include 'include-footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>