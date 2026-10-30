<?php
session_start();
require_once '../staff/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../staff/login.php");
    exit();
}

$lang = $_SESSION['idioma'] ?? 'es';

$textos = [
    'es' => [
        'titulo_pagina' => 'galería',
        'volver'        => 'Volver',
        'por'           => 'Por'
    ],
    'en' => [
        'titulo_pagina' => 'gallery',
        'volver'        => 'Back',
        'por'           => 'By'
    ]
];

$t = $textos[$lang];

$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del perfil del usuario logueado
$stmt_user = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmt_user->execute([$usuario_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);
$foto_perfil = $user['foto_perfil'] ?? null;

// Obtener las publicaciones solo del usuario activo junto con su nombre
$stmt = $pdo->prepare("SELECT g.imagen_url, g.titulo, g.fecha_creacion, u.nombre 
                       FROM galeria g 
                       JOIN usuarios u ON g.usuario_id = u.id 
                       WHERE g.usuario_id = ? 
                       ORDER BY g.id DESC");
$stmt->execute([$usuario_id]);
$publicaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($t['titulo_pagina']) ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .gallery-card img {
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .gallery-card img:hover {
            transform: scale(1.03);
        }

        .modal-zoom {
            display: none;
            position: fixed;
            z-index: 2000;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.88);
            backdrop-filter: blur(4px);
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
            box-sizing: border-box;
        }

        .modal-zoom.active {
            display: flex;
        }

        .modal-content-img {
            max-width: 90%;
            max-height: 70vh;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.6);
            object-fit: contain;
        }

        .modal-details {
            margin-top: 15px;
            text-align: center;
            color: #ffffff;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .modal-info {
            font-size: 0.95rem;
            opacity: 0.85;
        }

        .close-modal-btn {
            position: absolute;
            top: 20px;
            right: 25px;
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <a href="../index.php" class="nav-btn left-btn" title="<?= $t['volver'] ?>">&lt;</a>
        <h1 class="page-title"><?= $t['titulo_pagina'] ?></h1>
        <a href="../staff/usuario.php" class="profile-btn">
            <?php if (!empty($foto_perfil) && file_exists("../uploads/perfiles/" . $foto_perfil)): ?>
                <img src="../uploads/perfiles/<?= htmlspecialchars($foto_perfil) ?>" class="avatar-icon" style="object-fit: cover; border-radius: 50%;">
            <?php else: ?>
                <div class="avatar-icon"></div>
            <?php endif; ?>
        </a>
    </header>

    <main class="gallery-grid-container">
        <?php if (count($publicaciones) > 0): ?>
            <?php foreach ($publicaciones as $dibujo): ?>
                <?php 
                    // Limpieza de ruta (Windows vs Web)
                    $nombre_archivo = str_replace('\\', '/', $dibujo['imagen_url']); 

                    // Construir ruta probando carpeta con ID de usuario primero
                    if (strpos($nombre_archivo, '/') === false) {
                        $src_final = "../uploads/galeria/" . $usuario_id . "/" . $nombre_archivo;
                    } else {
                        $src_final = "../uploads/galeria/" . ltrim($nombre_archivo, '/');
                    }

                    // Fallback a la carpeta raíz galeria/ por si es un archivo antiguo
                    if (!file_exists($src_final)) {
                        $src_final = "../uploads/galeria/" . basename($nombre_archivo);
                    }

                    // Formato de fecha legible
                    $fecha = !empty($dibujo['fecha_creacion']) 
                        ? date("d/m/Y", strtotime($dibujo['fecha_creacion'])) 
                        : '';
                ?>
                <div class="gallery-card">
                    <img src="<?= htmlspecialchars($src_final); ?>" 
                         alt="<?= htmlspecialchars($dibujo['titulo'] ?? ''); ?>" 
                         data-titulo="<?= htmlspecialchars($dibujo['titulo'] ?? ''); ?>"
                         data-autor="<?= htmlspecialchars($dibujo['nombre'] ?? ''); ?>"
                         data-fecha="<?= $fecha; ?>"
                         onclick="abrirZoom(this)">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php for($i=0; $i<12; $i++): ?>
                <div class="gallery-card"></div>
            <?php endfor; ?>
        <?php endif; ?>
    </main>

    <div id="modalZoom" class="modal-zoom" onclick="cerrarZoom()">
        <span class="close-modal-btn">&times;</span>
        <img id="imgZoom" class="modal-content-img" src="" alt="Zoom">
        <div class="modal-details" onclick="event.stopPropagation()">
            <div id="tituloZoom" class="modal-title"></div>
            <div id="infoZoom" class="modal-info"></div>
        </div>
    </div>

    <script>
        const textoPor = <?= json_encode($t['por']) ?>;

        function abrirZoom(elemento) {
            const modal = document.getElementById('modalZoom');
            const img = document.getElementById('imgZoom');
            const titulo = document.getElementById('tituloZoom');
            const info = document.getElementById('infoZoom');

            img.src = elemento.src;
            titulo.textContent = elemento.getAttribute('data-titulo');
            info.textContent = textoPor + ': ' + elemento.getAttribute('data-autor') + ' • ' + elemento.getAttribute('data-fecha');

            modal.classList.add('active');
        }

        function cerrarZoom() {
            document.getElementById('modalZoom').classList.remove('active');
        }
    </script>

</body>
</html>