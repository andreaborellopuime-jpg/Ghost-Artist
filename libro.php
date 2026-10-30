<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../staff/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../staff/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Manejo de la simulación de Suscripción/Pago
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_suscripcion'])) {
    $nuevo_estado = (int)$_POST['suscrito_estado'];
    $stmtSub = $pdo->prepare("UPDATE usuarios SET suscrito = ? WHERE id = ?");
    $stmtSub->execute([$nuevo_estado, $usuario_id]);
    header("Location: libro.php");
    exit();
}

// Consultar datos actualizados del usuario
$stmt = $pdo->prepare("SELECT foto_perfil, rol, suscrito FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$foto_perfil = $user['foto_perfil'] ?? null;
$es_admin = (($user['rol'] ?? $_SESSION['rol'] ?? 'usuario') === 'admin');
$esta_suscrito = (bool)($user['suscrito'] ?? 0);

$lang = $_SESSION['idioma'] ?? 'es';

$textos = [
    'es' => [
        'titulo_pagina'     => 'Libro',
        'cambiar_lado'      => '⇄ Cambiar disposición',
        'limpiar'           => 'Limpiar',
        'pincel'            => 'Pincel',
        'goma'              => 'Goma',
        'anterior'          => '&lt; Anterior',
        'siguiente'         => 'Siguiente &gt;',
        'pagina'            => 'Página',
        'pizarra_titulo'    => 'Pizarrón',
        'seccion_videos'    => 'Videos Explicativos Exclusivos',
        'promo_sub_titulo'  => '¡Accede a la videoteca exclusiva!',
        'promo_sub_desc'    => 'Suscríbete para acceder a videos guiados explicativos paso a paso.',
        'btn_suscribirse'   => 'Suscribirse Ahora ($)',
        'btn_cancelar_sub'  => 'Cancelar Suscripción',
        'no_videos'         => 'Aún no hay videos explicativos cargados.'
    ],
    'en' => [
        'titulo_pagina'     => 'Book',
        'cambiar_lado'      => '⇄ Switch layout',
        'limpiar'           => 'Clear',
        'pincel'            => 'Brush',
        'goma'              => 'Eraser',
        'anterior'          => '&lt; Previous',
        'siguiente'         => 'Next &gt;',
        'pagina'            => 'Page',
        'pizarra_titulo'    => 'Board',
        'seccion_videos'    => 'Exclusive Explanatory Videos',
        'promo_sub_titulo'  => 'Unlock exclusive video library!',
        'promo_sub_desc'    => 'Subscribe to get access to step-by-step explanatory video guides.',
        'btn_suscribirse'   => 'Subscribe Now ($)',
        'btn_cancelar_sub'  => 'Cancel Subscription',
        'no_videos'         => 'No explanatory videos uploaded yet.'
    ]
];

$t = $textos[$lang];

// Consulta de las páginas del libro
$stmtPaginas = $pdo->query("SELECT * FROM libro_paginas ORDER BY orden ASC");
$paginasBD = $stmtPaginas ? $stmtPaginas->fetchAll(PDO::FETCH_ASSOC) : [];

$paginas = [];
foreach ($paginasBD as $pag) {
    $imgUrl = ($lang === 'en' && !empty($pag['imagen_en'])) ? $pag['imagen_en'] : $pag['imagen_es'];
       if (!empty($imgUrl)) {
       $paginas[] = ['imagen' => 'ver_libro.php?f=' . urlencode(basename($imgUrl))];
   }
}
foreach ($paginas as $i => $p) {
    $nombreArchivo = urldecode(str_replace('ver_libro.php?f=', '', $p['imagen']));
    $rutaFisica = __DIR__ . '/../uploads/libro/' . $nombreArchivo;
    error_log("Pagina ".($i+1).": ".$p['imagen']." -> ".(file_exists($rutaFisica) ? 'EXISTE' : 'NO EXISTE'));
}

// Cargar lista de videos explicativos
$videosList = [];
if ($esta_suscrito || $es_admin) {
    $stmtVideos = $pdo->query("SELECT * FROM videos_explicativos ORDER BY id DESC");
    $videosList = $stmtVideos ? $stmtVideos->fetchAll(PDO::FETCH_ASSOC) : [];
}

// Función auxiliar para renderizar el reproductor de video
function obtenerReproductorVideo($url) {
    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $url, $matches)) {
        return '<iframe src="https://www.youtube.com/embed/' . htmlspecialchars($matches[1]) . '" frameborder="0" allowfullscreen style="width:100%; height:200px; border-radius:6px;"></iframe>';
    }
    return '<video controls style="width:100%; max-height:220px; border-radius:6px; background:#000;"><source src="' . htmlspecialchars($url) . '">Tu navegador no soporta el reproductor de video.</video>';
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['titulo_pagina']) ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .profile-btn { position: relative; display: inline-block; }
        .admin-mask { position: absolute; top: -5px; left: -5px; width: calc(100% + 10px); height: calc(100% + 10px); pointer-events: none; z-index: 10; }

        .guide-image-container { position: relative; }
        .protegido { user-select: none; -webkit-user-select: none; }
        .shield { position: absolute; inset: 0; z-index: 5; }

        /* ===== Contenedor principal (mismo estilo que el panel admin) ===== */
        .admin-container {
            max-width: 1400px;
            margin: 40px auto 44px;
            background: linear-gradient(135deg, #2a2845, #3f3961);
            padding: 35px;
            border-radius: 15px;
            color: #fff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .toolbar-top { text-align: center; margin-bottom: 20px; }

        /* ===== Grid en 2 columnas ===== */
        .admin-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
            gap: 20px;
        }
        .admin-grid.inverted { grid-template-columns: minmax(0, 1fr) minmax(0, 1.5fr); }
        .admin-grid.inverted .panel-libro { order: 2; }
        .admin-grid.inverted .panel-pizarron { order: 1; }

        /* ===== Bloques (mismo estilo que .admin-section) ===== */
        .admin-section {
            background: rgba(63, 57, 97, 0.95);
            padding: 20px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            order: 3;
        }
        .admin-section:hover {
            box-shadow: 0 4px 20px rgba(0, 206, 201, 0.25);
        }
        /* Sólo los bloques pequeños hacen zoom; el pizarrón y el libro no,
           para no descuadrar el dibujo ni mover la imagen mientras se usan */
        .admin-section.zoomable:hover { transform: scale(1.02); }

        .admin-section.full-width { grid-column: 1 / -1; }

        .admin-section h2 { margin: 0; font-size: 1.2rem; }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #5a5286;
            padding-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }

        /* ===== Botones pequeños (estilo .btn-action del admin) ===== */
        .board-btn {
            background: #5a5286;
            color: #fff;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85rem;
            white-space: nowrap;
            transition: background 0.3s ease;
        }
        .board-btn:hover:not(:disabled) { background: #6c5ce7; }
        .board-btn:disabled { opacity: 0.45; cursor: not-allowed; }
        .board-btn.active { background: #6c5ce7; box-shadow: 0 0 6px rgba(108, 92, 231, 0.6); }
        .board-btn.btn-delete { background: #a83232; }
        .board-btn.btn-delete:hover { background: #c23c3c; }

        .board-controls { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        #brush-size { width: 60px; accent-color: #6c5ce7; }

        /* ===== Botón principal (estilo .btn-save del admin) ===== */
        .btn-save {
            background: linear-gradient(135deg, var(--lime-action, #00b894), #009473);
            color: var(--text-light, #fff);
            text-decoration: none;
            padding: 10px 30px;
            border: none;
            border-radius: 30px;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 184, 148, 0.4);
            transition: all 0.3s var(--transition-smooth, ease);
            display: inline-block;
            cursor: pointer;
        }
        .btn-save:hover {
            background: linear-gradient(135deg, var(--lime-hover, #55efc4), var(--lime-action, #00b894));
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 8px 22px rgba(85, 239, 196, 0.5);
            color: var(--purple-dark, #2a2845);
        }

        /* ===== Pizarrón ===== */
        .canvas-container {
            flex: 1;
            background: #2a2845;
            border: 1px solid #5a5286;
            border-radius: 6px;
            overflow: hidden;
            position: relative;
            min-height: 500px;
            height: 720px;
        }
        canvas { display: block; width: 100%; height: 100%; cursor: crosshair; touch-action: none; }

        /* ===== Libro ===== */
        .book-content { flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .guide-image-container {
            flex: 1;
            background-color: #ffffff;
            border: 1px solid #5a5286;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 500px;  
            height: 720px;
            overflow: hidden;
            padding: 10px;
        }
        .img-responsive { max-width: 100%; max-height: 100%; object-fit: contain; display: block; }
        .pagination-bar { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; }

        /* ===== Videos ===== */
        .subscription-banner {
            background: #2a2845;
            border: 1px solid #5a5286;
            text-align: center;
            padding: 30px 20px;
            border-radius: 10px;
        }
        .subscription-banner h3 { margin-top: 0; color: #55efc4; font-size: 1.4rem; }
        .subscription-banner .btn-save { margin-top: 10px; }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        .video-card {
            background: #2a2845;
            border-radius: 6px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .video-card:hover { transform: translateY(-2px); box-shadow: 0 2px 12px rgba(0, 0, 0, 0.3); }
        .video-card h4 { margin: 0 0 10px 0; font-size: 1rem; color: #fff; }

        /* ===== Pantallas pequeñas ===== */
        @media (max-width: 850px) {
            .admin-grid,
            .admin-grid.inverted { grid-template-columns: 1fr; }
            .admin-grid.inverted .panel-libro,
            .admin-grid.inverted .panel-pizarron { order: 0; }
            .canvas-container, .guide-image-container { height: 380px; min-height: 300px; }
        }
    </style>
</head>
<body class="main-body">

    <header class="top-bar">
        <a href="../index.php" class="nav-btn left-btn" title="Volver">&lt;</a>
        <h1 class="page-title"><?= htmlspecialchars(strtolower($t['titulo_pagina'])) ?></h1>

        <a href="../staff/usuario.php" class="profile-btn">
            <?php if (!empty($foto_perfil) && file_exists("../uploads/perfiles/" . $foto_perfil)): ?>
                <img src="../uploads/perfiles/<?= htmlspecialchars($foto_perfil) ?>" class="avatar-icon" style="object-fit: cover; border-radius: 50%;">
            <?php else: ?>
                <div class="avatar-icon"></div>
            <?php endif; ?>

            <?php if ($es_admin): ?>
                <img src="../pics/admin_mask.png" alt="Admin Frame" class="admin-mask">
            <?php endif; ?>
        </a>
    </header>

    <main class="admin-container">

        <div class="toolbar-top">
            <button onclick="toggleDisposicion()" class="board-btn" style="padding: 8px 16px;">
                <?= $t['cambiar_lado'] ?>
            </button>
        </div>

        <div class="admin-grid" id="workspace">

            <!-- BLOQUE 1: LIBRO -->
            <section class="admin-section panel-libro">
                <div class="panel-header">
                    <h2><?= $t['titulo_pagina'] ?></h2>
                    <span id="page-indicator"><?= $t['pagina'] ?> 1 / <?= count($paginas) ?></span>
                </div>
                <div class="book-content">
                    <div class="guide-image-container <?= $es_admin ? '' : 'protegido' ?>"
     <?= $es_admin ? '' : 'oncontextmenu="return false" ondragstart="return false"' ?>>
    <img id="book-img" src="" alt="Página del libro" class="img-responsive" draggable="false">
    <?php if (!$es_admin): ?>
        <div class="shield"></div>
    <?php endif; ?>
</div>
                    <div class="pagination-bar">
                        <button class="board-btn" id="btn-prev" onclick="cambiarPagina(-1)"><?= $t['anterior'] ?></button>
                        <button class="board-btn" id="btn-next" onclick="cambiarPagina(1)"><?= $t['siguiente'] ?></button>
                    </div>
                </div>
            </section>

            <!-- BLOQUE 2: PIZARRÓN -->
            <section class="admin-section panel-pizarron">
                <div class="panel-header">
                    <h2><?= $t['pizarra_titulo'] ?></h2>
                    <div class="board-controls">
                        <button class="board-btn active" id="tool-brush" onclick="setHerramienta('brush')"><?= $t['pincel'] ?></button>
                        <button class="board-btn" id="tool-eraser" onclick="setHerramienta('eraser')"><?= $t['goma'] ?></button>
                        <input type="range" id="brush-size" min="2" max="20" value="4">
                        <button class="board-btn" onclick="limpiarPizarron()"><?= $t['limpiar'] ?></button>
                    </div>
                </div>
                <div class="canvas-container" id="canvas-wrapper">
                    <canvas id="pizarron"></canvas>
                </div>
            </section>

            <!-- BLOQUE 3: VIDEOS EXPLICATIVOS Y SUSCRIPCIÓN -->
            <section class="admin-section full-width">
                <div>
                    <div class="panel-header">
                        <h2><?= htmlspecialchars($t['seccion_videos']) ?></h2>
                        <?php if ($esta_suscrito && !$es_admin): ?>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="action_suscripcion" value="1">
                                <input type="hidden" name="suscrito_estado" value="0">
                                <button type="submit" class="board-btn btn-delete"><?= $t['btn_cancelar_sub'] ?></button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if ($esta_suscrito || $es_admin): ?>
                        <?php if (!empty($videosList)): ?>
                            <div class="video-grid">
                                <?php foreach ($videosList as $vid): ?>
                                    <div class="video-card">
                                        <h4><?= htmlspecialchars($vid['titulo']) ?></h4>
                                        <?= obtenerReproductorVideo($vid['url']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color: #ccc; text-align: center; margin: 20px 0;"><?= htmlspecialchars($t['no_videos']) ?></p>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="subscription-banner">
                            <h3><?= htmlspecialchars($t['promo_sub_titulo']) ?></h3>
                            <p><?= htmlspecialchars($t['promo_sub_desc']) ?></p>
                            <form method="POST" style="display:inline-block;">
                                <input type="hidden" name="action_suscripcion" value="1">
                                <input type="hidden" name="suscrito_estado" value="1">
                                <button type="submit" class="btn-save"><?= htmlspecialchars($t['btn_suscribirse']) ?></button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

        </div>
    </main>

    <script>
        const paginas = <?= json_encode($paginas) ?>;
        let paginaActual = 0;

        function renderPagina() {
            if (paginas.length === 0) {
                document.getElementById('book-img').style.display = 'none';
                document.getElementById('page-indicator').textContent = `<?= $t['pagina'] ?> 0 / 0`;
                document.getElementById('btn-prev').disabled = true;
                document.getElementById('btn-next').disabled = true;
                return;
            }

            const data = paginas[paginaActual];
            const imgEl = document.getElementById('book-img');
            imgEl.style.display = 'block';
            imgEl.src = data.imagen;

            document.getElementById('page-indicator').textContent = `<?= $t['pagina'] ?> ${paginaActual + 1} / ${paginas.length}`;
            document.getElementById('btn-prev').disabled = (paginaActual === 0);
            document.getElementById('btn-next').disabled = (paginaActual === paginas.length - 1);
        }

        function cambiarPagina(delta) {
            if (paginaActual + delta >= 0 && paginaActual + delta < paginas.length) {
                paginaActual += delta;
                renderPagina();
            }
        }

        function toggleDisposicion() {
            const workspace = document.getElementById('workspace');
            workspace.classList.toggle('inverted');
            ajustarCanvas();
        }

        const canvas = document.getElementById('pizarron');
        const ctx = canvas.getContext('2d');
        const wrapper = document.getElementById('canvas-wrapper');

        let dibujando = false;
        let herramienta = 'brush';

        function ajustarCanvas() {
            canvas.width = wrapper.clientWidth;
            canvas.height = wrapper.clientHeight;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
        }

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return {
                x: (clientX - rect.left) * (canvas.width / rect.width),
                y: (clientY - rect.top) * (canvas.height / rect.height)
            };
        }

        function empezarDibujo(e) {
            dibujando = true;
            const pos = getPos(e);
            ctx.beginPath();
            ctx.moveTo(pos.x, pos.y);
        }

        function dibujar(e) {
            if (!dibujando) return;
            e.preventDefault();
            const pos = getPos(e);
            const grosor = document.getElementById('brush-size').value;

            ctx.lineWidth = grosor;
            ctx.strokeStyle = (herramienta === 'brush') ? '#ffffff' : '#2a2845';

            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();
        }

        function detenerDibujo() { dibujando = false; }

        function setHerramienta(tool) {
            herramienta = tool;
            document.getElementById('tool-brush').classList.toggle('active', tool === 'brush');
            document.getElementById('tool-eraser').classList.toggle('active', tool === 'eraser');
        }

        function limpiarPizarron() { ctx.clearRect(0, 0, canvas.width, canvas.height); }

        canvas.addEventListener('mousedown', empezarDibujo);
        canvas.addEventListener('mousemove', dibujar);
        canvas.addEventListener('mouseup', detenerDibujo);
        canvas.addEventListener('mouseleave', detenerDibujo);

        canvas.addEventListener('touchstart', empezarDibujo);
        canvas.addEventListener('touchmove', dibujar);
        canvas.addEventListener('touchend', detenerDibujo);

        window.addEventListener('resize', ajustarCanvas);

        window.onload = function() {
            renderPagina();
            ajustarCanvas();
        };
    </script>
    <?php include __DIR__ . '/../staff/tour_include.php'; ?>
</body>
</html>