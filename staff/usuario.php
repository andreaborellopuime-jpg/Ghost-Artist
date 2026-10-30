<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$lang = $_SESSION['idioma'] ?? 'es';

$textos = [
    'es' => [
        'titulo_pagina'     => 'Perfil',
        'volver'            => 'Volver',
        'editar_perfil'     => 'Editar perfil',
        'nivel'             => 'nivel',
        'dibujos_recientes' => 'dibujos recientes',
        'bio_defecto'       => 'Sin biografía aún.'
    ],
    'en' => [
        'titulo_pagina'     => 'Profile',
        'volver'            => 'Back',
        'editar_perfil'     => 'Edit profile',
        'nivel'             => 'level',
        'dibujos_recientes' => 'recent drawings',
        'bio_defecto'       => 'No bio yet.'
    ]
];

$t = $textos[$lang];
$usuario_id = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: login.php?error=usuario_no_encontrado");
    exit();
}

$nombre = $user['nombre'] ?? 'user4285';
$bio = !empty($user['bio']) ? $user['bio'] : $t['bio_defecto'];
$puntos = (int)($user['puntos'] ?? 0);

// Cálculo dinámico de niveles
$puntos_por_nivel = 50;
$nivel_numero = (int)floor($puntos / $puntos_por_nivel) + 1;
$puntos_nivel_actual = $puntos % $puntos_por_nivel;
$porcentaje_barra = ($puntos_nivel_actual / $puntos_por_nivel) * 100;

$stmt_galeria = $pdo->prepare("SELECT * FROM galeria WHERE usuario_id = ? ORDER BY id DESC LIMIT 6");
$stmt_galeria->execute([$usuario_id]);
$dibujos = $stmt_galeria->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['titulo_pagina']) ?></title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <div class="profile-header-bg">
        <a href="../index.php" class="nav-btn left-btn" title="<?= htmlspecialchars($t['volver']) ?>">&lt;</a>
 
        <a href="../extras/editar_perfil.php" class="nav-btn right-btn" title="<?= htmlspecialchars($t['editar_perfil']) ?>">
            <img src="../pics/styler_icon.png" alt="<?= htmlspecialchars($t['editar_perfil']) ?>" style="width: 28px; height: 28px; object-fit: contain;">
        </a>

        <?php if (!empty($user['foto_perfil']) && file_exists("../uploads/perfiles/" . $user['foto_perfil'])): ?>
            <img src="../uploads/perfiles/<?= htmlspecialchars($user['foto_perfil']) ?>" class="profile-avatar-large" style="object-fit: cover;">
        <?php else: ?>
            <img src="../pics/defaut_icon.png" class="profile-avatar-large" style="object-fit: cover;" alt="<?= htmlspecialchars($nombre) ?>">
        <?php endif; ?>
    </div>

    <div class="user-info-text">
        <h2><?= htmlspecialchars($nombre) ?></h2>
        <!-- Muestra la Bio en lugar del nivel anterior -->
        <p><?= htmlspecialchars($bio) ?></p>
    </div>

    <div class="level-container">
        <div class="level-bar-box">
            <span><?= htmlspecialchars($t['nivel']) ?></span>
            <div class="level-progress-bg">
                <div class="level-progress-fill" style="width: <?= $porcentaje_barra ?>%;"></div>
            </div>
            <span><?= $nivel_numero ?></span>
        </div>
    </div>

    <div class="recent-drawings-card">
        <div class="recent-drawings-header">
            <?= htmlspecialchars($t['dibujos_recientes']) ?>
        </div>
        <div class="recent-drawings-grid">
            <?php if (count($dibujos) > 0): ?>
                <?php foreach ($dibujos as $dibujo): ?>
                    <?php 
                        $archivo = str_replace('\\', '/', $dibujo['imagen_url']);
                        $src_final = (strpos($archivo, '/') === false) 
                            ? "../uploads/galeria/" . $usuario_id . "/" . $archivo 
                            : "../uploads/galeria/" . ltrim($archivo, '/');
                        if (!file_exists($src_final)) {
                            $src_final = "../uploads/galeria/" . basename($archivo);
                        }
                    ?>
                    <div class="gallery-card">
                        <img src="<?= htmlspecialchars($src_final) ?>" alt="<?= htmlspecialchars($dibujo['titulo'] ?? '') ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php for($i=0; $i<6; $i++): ?>
                    <div class="gallery-card"></div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>
<?php include __DIR__ . '/../staff/tour_include.php'; ?>
</body>
</html>