<?php
session_start();
require_once 'staff/database.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: staff/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consulta explícita a la base de datos
$stmt = $pdo->prepare("SELECT foto_perfil, rol FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $foto_perfil = $user['foto_perfil'] ?? null;
    // Sobreescribir la sesión con el valor exacto de la BD
    $_SESSION['rol'] = $user['rol']; 
}

$lang = $_SESSION['idioma'] ?? 'es';

$textos = [
    'es' => [
        'galeria'        => 'galería',
        'idioma'         => 'idioma',
        'donaciones'     => 'donaciones',
        'panel de edicion' => 'panel de edición',
        'cerrar_sesion'  => 'cerrar sesión',
        'libro'          => 'libro',
        'tarea'          => 'tarea',
        'desafio'        => 'desafío diario',
        'btn_ver'        => 'ver'
    ],
    'en' => [
        'galeria'        => 'gallery',
        'idioma'         => 'language',
        'donaciones'     => 'donations',
        'panel de edicion' => 'editing panel',
        'cerrar_sesion'  => 'log out',
        'libro'          => 'book',
        'tarea'          => 'homework',
        'desafio'        => 'daily challenge',
        'btn_ver'        => 'view'
    ]
];

$t = $textos[$lang];

$es_admin = (($_SESSION['rol'] ?? 'usuario') === 'admin');

?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ghost Artist</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos auxiliares para superponer el marco del admin */
        .profile-btn {
            position: relative;
            display: inline-block;
        }
        .admin-mask {
            position: absolute;
            top: -5px;
            left: -5px;
            width: calc(100% + 10px);
            height: calc(100% + 10px);
            pointer-events: none; /* Permite hacer clic a través de la máscara */
            z-index: 10;
        }
    </style>
</head>
<body>

    <aside id="sidebar" class="sidebar">
        <nav class="sidebar-nav">
            <a href="extras/galeria.php" class="sidebar-link"><?= $t['galeria'] ?></a>
            <a href="extras/idioma.php" class="sidebar-link"><?= $t['idioma'] ?></a>
            <a href="extras/tips.php" class="sidebar-link"><?= $t['donaciones'] ?></a>

        <?php if ($es_admin): ?>
            <a href="extras/admin_panel.php" class="sidebar-link" style="color: #9cb043; font-weight: bold;">
                <?= $t['panel de edicion'] ?>
            </a>
        <?php endif; ?>

            <a href="staff/logout.php" class="sidebar-link"><?= $t['cerrar_sesion'] ?></a>
        </nav>
    </aside>

    <div id="overlay" class="overlay" onclick="toggleMenu()"></div>

    <header class="top-bar">
        <button class="menu-btn" id="menuBtn" onclick="toggleMenu()">
            <img src="pics/sidebar_con.png" alt="Menú" class="menu-icon">
        </button>
        
    <a href="staff/usuario.php" class="profile-btn">
        <?php if (!empty($foto_perfil) && file_exists("uploads/perfiles/" . $foto_perfil)): ?>
    <img src="uploads/perfiles/<?= htmlspecialchars($foto_perfil) ?>" class="avatar-icon" style="object-fit: cover; border-radius: 50%;">
        <?php else: ?>
    <img src="pics/defaut_icon.png" class="avatar-icon" style="object-fit: cover; border-radius: 50%;" alt="Avatar">
        <?php endif; ?>

        <?php if ($es_admin): ?>
    <img src="pics/admin_mask.png" alt="Admin Frame" class="admin-mask">
        <?php endif; ?>
</a>
    </header>

    <main class="dashboard-container">
        
        <section class="card card-libro">
            <img src="pics/librito.jpeg" alt="Libro" class="card-bg-img">
            <div class="card-content">
                <h2 class="card-title"><?= $t['libro'] ?></h2>
                <a href="actions/libro.php" class="btn-action"><?= $t['btn_ver'] ?></a>
            </div>
        </section>

        <section class="card card-tarea">
            <img src="pics/tarea.jpeg" alt="Tarea" class="card-bg-img">
            <div class="card-content align-right">
                <a href="actions/tarea.php" class="btn-action"><?= $t['btn_ver'] ?></a>
                <h2 class="card-title"><?= $t['tarea'] ?></h2>
            </div>
        </section>

        <section class="card card-desafio">
            <img src="pics/desafio.jpeg" alt="Desafío Diario" class="card-bg-img">
            <div class="card-content">
                <h2 class="card-title"><?= $t['desafio'] ?></h2>
                <a href="actions/desafio.php" class="btn-action"><?= $t['btn_ver'] ?></a>
            </div>
        </section>

    </main>

    <script>
        function toggleMenu() {
            document.getElementById('sidebar').classList.toggle('active');
            document.getElementById('overlay').classList.toggle('active');
            document.getElementById('menuBtn').classList.toggle('active');
        }
    </script>
    <?php include __DIR__ . '/staff/tour_include.php'; ?>
<br>
</body>
</html>