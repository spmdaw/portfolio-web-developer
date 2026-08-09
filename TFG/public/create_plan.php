<?php
session_start();
require_once "../src/config.php";

$userId = $_SESSION['user_id'] ?? 1;

$mensaje = "";

//  PROCESAR FORMULARIO
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $titulo   = trim($_POST['titulo'] ?? "");
  $descripcion = trim($_POST['descripcion'] ?? "");
  $categoria  = trim($_POST['categoria'] ?? "");
  $direccion  = trim($_POST['direccion'] ?? "");
  $lat     = isset($_POST['latitude']) && $_POST['latitude'] !== "" ? floatval($_POST['latitude']) : null;
  $lng     = isset($_POST['longitude']) && $_POST['longitude'] !== "" ? floatval($_POST['longitude']) : null;
  $imagenSubida = isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK;

  // COMPROBAMOS SI TODOS LOS CAMPOS DEL FORMULARIO SE HAN RELLENADO CORRECTAMENTE Y NINGUNO ESTE VACIO, EN EL CASO CONTRARIO MOSTRARA UN ERORR AL USUARIO
  if ($titulo === "" || $descripcion === "" || $categoria === "" || $direccion === "" || $lat === null || $lng === null || !$imagenSubida) {
    $mensaje = "Todos los campos son obligatorios, incluida la ubicación en el mapa y la imagen";
  } else {
    //  SUBIR IMAGEN
    $rutaImagen = null;

    $nombreTmp = $_FILES['imagen']['tmp_name'];
    $nombreOriginal = $_FILES['imagen']['name'];

    $carpetaDestino = "./assets/images/plans/";

    if (!file_exists($carpetaDestino)) {
      mkdir($carpetaDestino, 0777, true);
    }
$nombreNuevo = uniqid("plan_") . "_" . basename($nombreOriginal);
$rutaFinal = $carpetaDestino . $nombreNuevo;

    // GUARDAR CON EL NOMBRE ORIGINAL
    $nombreNuevo = $nombreOriginal;
    $rutaFinal = $carpetaDestino . $nombreNuevo;

    if (move_uploaded_file($nombreTmp, $rutaFinal)) {
      $rutaImagen = $rutaFinal;
    } else {
      $mensaje = "Error al subir la imagen";
    }

    // ALMCENA LOS DATOS DEL PLAN EN FORMATO JSON
    if ($mensaje === "") {
      $sql = "INSERT INTO plans (title, description, category, direccion, lat, lng, image, created_by)
        VALUES (:title, :description, :category, :direccion, :lat, :lng, :image, :created_by)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        ":title"   => $titulo,
        ":description" => $descripcion,
        ":category"  => $categoria,
        ":direccion" => $direccion,
        ":lat"    => $lat,
        ":lng"    => $lng,
        ":image"   => $rutaImagen,
        ":created_by" => $userId
      ]);

      $mensaje = "Plan creado correctamente";

      echo '<script>';
      echo 'setTimeout(function() {';
      echo '  window.location.href = "dashboard.php";';
      echo '}, 1250);';
      echo '</script>';
      $titulo = $descripcion = $categoria = $direccion = "";
      $lat = $lng = null;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear plan</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <link href="assets/images/icon.jpg" rel="icon">
  <style>
    #map {
      height: 300px;
      width: 100%;
      border-radius: 10px;
      border: 1px solid #CED4DA;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
      overflow: hidden;
      margin-top: 8px;
    }

    #map:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
    }

    .leaflet-control-zoom a {
      background: #fff !important;
      border: 1px solid #CED4DA !important;
      color: #495057 !important;
      border-radius: .375rem !important;
      width: 36px !important;
      height: 36px !important;
      line-height: 34px !important;
      font-size: 20px !important;
    }

    .leaflet-control-zoom a:hover {
      background: #E9ECEF !important;
      border-color: #ADB5BD !important;
    }

    .input-error {
      border-color: red !important;
    }

    label.error-label {
      color: red !important;
      font-weight: bold;
    }

    label.error-label::after {
      content: " *";
      color: red;
      font-weight: bold;
    }
  </style>
</head>

<body class="body2">
  <?php include './include-header.php'; ?>

  <div class="form-wrapper">
    <div class="form-card">
      <h1>Crear plan</h1>

      <?php if ($mensaje): ?>
        <div class="alert alert-info"> <?= htmlspecialchars($mensaje) ?> </div>
      <?php endif; ?>

      <form id="planForm" action="" method="POST" enctype="multipart/form-data">
        <div class="form-group smooth">
          <label for="titulo">Título del plan</label>
          <input type="text" name="titulo" class="input-field" id="titulo" placeholder="Ej: Pasear por el parque..." value="<?= htmlspecialchars($titulo ?? '') ?>">
        </div>

        <div class="form-group smooth">
          <label for="descripcion">Descripción</label>
          <textarea name="descripcion" class="input-field textarea" id="descripcion" rows="3" placeholder="Describe tu plan..."><?= htmlspecialchars($descripcion ?? '') ?></textarea>
        </div>

        <div class="form-group smooth">
          <label for="categoria">Categoría</label>
          <select name="categoria" class="input-field select" id="categoria">
            <option <?= ($categoria ?? '') === 'Feliz' ? 'selected' : '' ?>>Feliz</option>
            <option <?= ($categoria ?? '') === 'Triste' ? 'selected' : '' ?>>Triste</option>
            <option <?= ($categoria ?? '') === 'Enfadado' ? 'selected' : '' ?>>Enfadado</option>
            <option <?= ($categoria ?? '') === 'Sorprendido' ? 'selected' : '' ?>>Sorprendido</option>
            <option <?= ($categoria ?? '') === 'Enamorado' ? 'selected' : '' ?>>Enamorado</option>
          </select>
          <input type="hidden" id="direccion" name="direccion" value="<?= htmlspecialchars($direccion ?? '') ?>">
        </div>

        <div class="form-group smooth">
          <label for="address">Dirección</label>
          <div style="display:flex; gap:10px;">
            <input type="text" id="address" placeholder="Escribe una dirección..." class="input-field" style="flex:1;" value="<?= htmlspecialchars($direccion ?? '') ?>">
            <button type="button" id="searchBtn" class="btn-submit" style="width:120px;">
              <i class="bi bi-search"></i> Buscar
            </button>
          </div>
        </div>

        <div class="form-group smooth">
          <label for="map">Ubicación del plan</label>
          <div id="map"></div>
          <input type="hidden" id="latitude" name="latitude" value="<?= htmlspecialchars($lat ?? '') ?>">
          <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($lng ?? '') ?>">
        </div>

        <div class="form-group smooth">
          <label for="imagen">Imagen</label><br>
          <input type="file" name="imagen" class="file-input" id="imagen">
        </div>

        <button type="submit" class="btn-submit">Crear plan</button>
      </form>
    </div>
  </div>

  <script>
    const API_KEY = "6bf5e1e9fbae4d92aa61c8875ff6f006";

    document.addEventListener("DOMContentLoaded", function() {
      const initialLat = parseFloat(document.getElementById('latitude').value) || 0;
      const initialLng = parseFloat(document.getElementById('longitude').value) || 0;

      var map = L.map('map').setView([initialLat, initialLng], initialLat && initialLng ? 15 : 2);
      L.tileLayer(`https://maps.geoapify.com/v1/tile/positron/{z}/{x}/{y}.png?apiKey=${API_KEY}`, {
        maxZoom: 20,
        attribution: "© Geoapify"
      }).addTo(map);
      var marker = L.marker([initialLat, initialLng], {
        draggable: true
      }).addTo(map);

      function updateInputs(lat, lon) {
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;
      }

      marker.on('dragend', e => updateInputs(e.target.getLatLng().lat, e.target.getLatLng().lng));
      map.on('click', e => {
        marker.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
      });

      document.getElementById('searchBtn').addEventListener('click', function() {
        const query = document.getElementById('address').value;
        if (!query) return;
        fetch(`https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(query)}&apiKey=${API_KEY}`)
          .then(res => res.json())
          .then(data => {
            if (data.features && data.features.length > 0) {
              const feat = data.features[0];
              marker.setLatLng([feat.properties.lat, feat.properties.lon]);
              map.setView([feat.properties.lat, feat.properties.lon], 15);
              updateInputs(feat.properties.lat, feat.properties.lon);
              document.getElementById('direccion').value = feat.properties.formatted;
            } else {
              alert("No se encontró la dirección");
            }
          }).catch(err => alert("Error al buscar la dirección: " + err));
      });


      const campos = ['titulo', 'descripcion', 'categoria', 'direccion', 'latitude', 'longitude', 'imagen'];

      campos.forEach(id => {
        const input = document.getElementById(id);
        input.addEventListener('input', () => {
          const label = input.closest('.form-group').querySelector('label');
          input.classList.remove('input-error');
          label.classList.remove('error-label');
        });
        if (id === 'imagen') {
          input.addEventListener('change', () => {
            const label = input.closest('.form-group').querySelector('label');
            input.classList.remove('input-error');
            label.classList.remove('error-label');
          });
        }
      });

      document.getElementById('planForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let valido = true;
        campos.forEach(id => {
          const input = document.getElementById(id);
          const label = input.closest('.form-group').querySelector('label');
          input.classList.remove('input-error');
          label.classList.remove('error-label');
          if (!input.value || (id === 'imagen' && input.files.length === 0)) {
            valido = false;
            input.classList.add('input-error');
            label.classList.add('error-label');
          }
        });
        if (valido) this.submit();
      });
    });
  </script>

  <?php include './include-footer.php'; ?>
</body>

</html>