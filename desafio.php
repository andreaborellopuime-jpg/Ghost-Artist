<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../staff/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../staff/login.php");
    exit();
}

$lang = $_SESSION['idioma'] ?? 'es';

$textos = [
    'es' => [
        'titulo_pagina'    => 'desafío diario',
        'instruccion'      => 'realizar el dibujo de un hada sentada en la rama de un árbol, utilizando los colores de una manzana roja. debe ser de cuerpo completo y el resto de los detalles son a libre criterio.',
        'label_titulo'     => 'Título de tu dibujo:',
        'ph_titulo'        => 'Escribe un título...',
        'btn_elegir'       => 'elegir imagen',
        'btn_subir'        => 'subir',
        'sin_titulo'       => 'Sin título',
        'completado_msj'   => '¡Ya has entregado tu dibujo para este desafío!',
        'ver_galeria'      => 'Ver en la galería'
    ],
    'en' => [
        'titulo_pagina'    => 'daily challenge',
        'instruccion'      => 'Draw a fairy sitting on a tree branch using the color palette of a red apple. It must be full-body; all other details are up to your imagination.',
        'label_titulo'     => 'Title of your drawing:',
        'ph_titulo'        => 'Type a title...',
        'btn_elegir'       => 'choose image',
        'btn_subir'        => 'upload',
        'sin_titulo'       => 'Untitled',
        'completado_msj'   => 'You have already submitted your drawing for this challenge!',
        'ver_galeria'      => 'View in gallery'
    ]
];

$t = $textos[$lang];

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT foto_perfil, rol FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$foto_perfil = $user['foto_perfil'] ?? null;
$es_admin = (($user['rol'] ?? $_SESSION['rol'] ?? 'usuario') === 'admin');

// OBTENER CONSIGNA DESDE LA BASE DE DATOS
$desafio_id = 1;
$stmtDesafioBD = $pdo->prepare("SELECT * FROM desafios WHERE id = ?");
$stmtDesafioBD->execute([$desafio_id]);
$desafioBD = $stmtDesafioBD ? $stmtDesafioBD->fetch(PDO::FETCH_ASSOC) : null;

if ($desafioBD) {
    if ($lang === 'en' && !empty($desafioBD['descripcion_en'])) {
        $consigna_dinamica = $desafioBD['descripcion_en'];
    } else {
        $consigna_dinamica = !empty($desafioBD['descripcion_es']) ? $desafioBD['descripcion_es'] : $desafioBD['descripcion'];
    }
} else {
    $consigna_dinamica = $t['instruccion']; 
}

// VERIFICAR SI EL USUARIO YA COMPLETÓ ESTE DESAFÍO
$stmtCheckEntrega = $pdo->prepare("SELECT id FROM galeria WHERE usuario_id = ? AND desafio_id = ?");
$stmtCheckEntrega->execute([$usuario_id, $desafio_id]);
$ya_entregado = (bool)$stmtCheckEntrega->fetch();

// PROCESAR SUBIDA DE IMAGEN (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen_dibujo'])) {
    
    // Si ya lo entregó previamente, cancelar el proceso
    if ($ya_entregado) {
        header("Location: desafio.php");
        exit();
    }

    $desafio_id_post = !empty($_POST['desafio_id']) ? (int)$_POST['desafio_id'] : null;

    if ($desafio_id_post) {
        $check_stmt = $pdo->prepare("SELECT id FROM desafios WHERE id = ?");
        $check_stmt->execute([$desafio_id_post]);
        if ($check_stmt->fetch()) {
            $desafio_id = $desafio_id_post;
        }
    }

    $titulo = !empty(trim($_POST['titulo'])) ? trim($_POST['titulo']) : $t['sin_titulo'];

    $directorio = "../uploads/galeria/";
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }

    $nombre_archivo = time() . "_" . basename($_FILES["imagen_dibujo"]["name"]);
    $ruta_final = $directorio . $nombre_archivo;

    if (move_uploaded_file($_FILES["imagen_dibujo"]["tmp_name"], $ruta_final)) {
        $stmt = $pdo->prepare("INSERT INTO galeria (usuario_id, desafio_id, titulo, imagen_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuario_id, $desafio_id, $titulo, $nombre_archivo]);

        $pdo->prepare("UPDATE usuarios SET puntos = puntos + 20 WHERE id = ?")->execute([$usuario_id]);

        header("Location: ../extras/galeria.php?exito=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($t['titulo_pagina']) ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .profile-btn { position: relative; display: inline-block; }
        .admin-mask { position: absolute; top: -5px; left: -5px; width: calc(100% + 10px); height: calc(100% + 10px); pointer-events: none; z-index: 10; }
        
        .completed-card {
            text-align: center;
            padding: 30px 20px;
        }
        .completed-icon {
            font-size: 3rem;
            color: #9cb043;
            margin-bottom: 10px;
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .file-upload-wrapper { position: relative; }
    </style>
</head>
<body class="main-body">

    <header class="top-bar">
        <a href="../index.php" class="nav-btn left-btn" title="Volver">&lt;</a>
        <h1 class="page-title"><?= $t['titulo_pagina'] ?></h1>
        
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

    <main class="content-container">

        <?php if ($ya_entregado): ?>
            <!-- SI EL USUARIO YA SUBIÓ FOTO EN ESTE DESAFÍO -->
            <div class="card-white completed-card">
                <div class="completed-icon">✓</div>
                <h2><?= htmlspecialchars($t['completado_msj']) ?></h2>
                <p class="instruction-text" style="margin-top: 15px;">
                    <?= htmlspecialchars($consigna_dinamica ?? '') ?>
                </p>
                <div style="margin-top: 25px;">
                    <a href="../extras/galeria.php" class="btn-action" style="text-decoration: none; display: inline-block;">
                        <?= htmlspecialchars($t['ver_galeria']) ?>
                    </a>
                </div>
            </div>

        <?php else: ?>
            <!-- FORMULARIO HABITUAL SI NO HA SUBIDO DIBUJO -->
            <form action="desafio.php" method="POST" enctype="multipart/form-data" class="desafio-form">
                <input type="hidden" name="desafio_id" value="1">

                <div class="card-white">
                    <p class="instruction-text">
                        <?= htmlspecialchars($consigna_dinamica ?? '') ?>
                    </p>

                    <div style="margin-bottom: 20px;">
                        <label for="titulo" style="display: block; font-weight: bold; margin-bottom: 8px; color: #0d0c1d;">
                            <?= $t['label_titulo'] ?>
                        </label>
                        <input type="text" id="titulo" name="titulo" placeholder="<?= $t['ph_titulo'] ?>" required class="text-input" style="width: 100%; box-sizing: border-box; font-size: 1rem; padding: 10px;">
                    </div>

                    <div class="file-upload-wrapper">
    <label for="imagen_dibujo" class="btn-grey"><?= $t['btn_elegir'] ?></label>
    <input type="file" id="imagen_dibujo" name="imagen_dibujo" accept="image/*" required>
    <span id="nombre_archivo" style="margin-left: 10px; font-size: 0.9rem;"></span>
</div>

<script>
document.getElementById('imagen_dibujo').addEventListener('change', function () {
    document.getElementById('nombre_archivo').textContent = this.files[0] ? this.files[0].name : '';
});
</script>
                </div><br>

                <div class="submit-wrapper">
                    <button type="submit" class="btn-action btn-submit"><?= $t['btn_subir'] ?></button>
                </div>
            </form>
        <?php endif; ?>

    </main>
<?php include __DIR__ . '/../staff/tour_include.php'; ?>
</body>
</html>