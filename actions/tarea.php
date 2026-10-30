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
        'titulo_pagina' => 'tarea',
        'placeholder'   => 'Tu respuesta...',
        'btn_responder' => 'responder',
        'intentos_rest' => 'Intentos restantes:',
        'sin_intentos'  => 'Te quedaste sin intentos.',
        'ejemplo'       => 'Ejemplo de respuesta correcta:',
        'acertaste'     => '¡Respuesta correcta!'
    ],
    'en' => [
        'titulo_pagina' => 'homework',
        'placeholder'   => 'Your answer...',
        'btn_responder' => 'submit',
        'intentos_rest' => 'Attempts left:',
        'sin_intentos'  => 'You have run out of attempts.',
        'ejemplo'       => 'Example of a correct answer:',
        'acertaste'     => 'Correct answer!'
    ]
];

$t = $textos[$lang];

$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT foto_perfil, rol FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$foto_perfil = $user['foto_perfil'] ?? null;
$es_admin = (($user['rol'] ?? $_SESSION['rol'] ?? 'usuario') === 'admin');

// Cantidad máxima de intentos por pregunta
$max_intentos = 3;

// PROCESAMIENTO DE RESPUESTAS (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tarea_id = filter_input(INPUT_POST, 'tarea_id', FILTER_VALIDATE_INT);
    $respuesta_usuario = mb_strtolower(trim($_POST['respuesta'] ?? ''));

    if ($tarea_id && $respuesta_usuario !== '') {
        $stmt = $pdo->prepare("SELECT respuesta_correcta, puntos_recompensa FROM tareas WHERE id = ?");
        $stmt->execute([$tarea_id]);
        $tarea = $stmt->fetch();

        if ($tarea) {
            $check_stmt = $pdo->prepare("SELECT id, es_correcta, intentos FROM tareas_respondidas WHERE usuario_id = ? AND tarea_id = ?");
            $check_stmt->execute([$usuario_id, $tarea_id]);
            $previo = $check_stmt->fetch();

            // Se bloquea si ya la acertó o si agotó los intentos
            $bloqueada = $previo && ($previo['es_correcta'] == 1 || (int)$previo['intentos'] >= $max_intentos);

            if (!$bloqueada) {
                // Respuestas válidas: palabras clave separadas por coma en MySQL
                $palabras_clave = explode(',', mb_strtolower($tarea['respuesta_correcta']));
                $es_correcta = 0;

                foreach ($palabras_clave as $clave) {
                    $clave = trim($clave);
                    if ($clave === '') continue; // evita que una coma de más acepte cualquier respuesta
                    if (mb_strpos($respuesta_usuario, $clave) !== false) {
                        $es_correcta = 1;
                        break;
                    }
                }

                if (!$previo) {
                    $pdo->prepare("INSERT INTO tareas_respondidas (usuario_id, tarea_id, es_correcta, intentos) VALUES (?, ?, ?, 1)")
                        ->execute([$usuario_id, $tarea_id, $es_correcta]);
                } else {
                    $pdo->prepare("UPDATE tareas_respondidas SET es_correcta = ?, intentos = intentos + 1 WHERE usuario_id = ? AND tarea_id = ?")
                        ->execute([$es_correcta, $usuario_id, $tarea_id]);
                }

                if ($es_correcta) {
                    $pdo->prepare("UPDATE usuarios SET puntos = puntos + ? WHERE id = ?")
                        ->execute([$tarea['puntos_recompensa'], $usuario_id]);
                }
            }
        }
    }
    header("Location: tarea.php");
    exit();
}

// OBTENER HISTORIAL Y LISTA DE PREGUNTAS (GET)
$tareas = $pdo->query("SELECT * FROM tareas ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

$historial_stmt = $pdo->prepare("SELECT tarea_id, es_correcta, intentos FROM tareas_respondidas WHERE usuario_id = ?");
$historial_stmt->execute([$usuario_id]);
// Resultado: [tarea_id => ['es_correcta' => 0|1, 'intentos' => N]]
$respuestas_guardadas = $historial_stmt->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);

function obtenerEstadoCirculo($tarea_id, $respuestas_guardadas) {
    if (array_key_exists($tarea_id, $respuestas_guardadas)) {
        return $respuestas_guardadas[$tarea_id]['es_correcta'] == 1
            ? ['symbol' => '✓', 'class' => 'status-correct']
            : ['symbol' => '✕', 'class' => 'status-incorrect'];
    }
    return ['symbol' => 'O', 'class' => 'status-neutral'];
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

        /* Permite una línea de información debajo de cada pregunta */
        .card-white-row { flex-wrap: wrap; }
        .task-info { flex-basis: 100%; margin-top: 12px; font-size: 0.85rem; color: #64748b; }
        .task-info.task-ok { color: #047857; font-weight: 700; }
        .task-info.task-example { background: #f1f5f9; border-left: 4px solid #6c5ce7; border-radius: 8px; padding: 10px 14px; color: #2d3436; }
        .task-info.task-example strong { color: #372048; }
        .text-input:disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; }
        .btn-action:disabled { opacity: 0.45; cursor: not-allowed; transform: none; }
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
        <?php foreach ($tareas as $item): ?>
            <?php 
                $status = obtenerEstadoCirculo($item['id'], $respuestas_guardadas); 
                $pregunta_mostrar = ($lang === 'en' && !empty($item['pregunta_en'])) 
                    ? $item['pregunta_en'] 
                    : (!empty($item['pregunta_es']) ? $item['pregunta_es'] : $item['pregunta']);

                // Estado de intentos
                $estado          = $respuestas_guardadas[$item['id']] ?? null;
                $intentos_usados = $estado ? (int)$estado['intentos'] : 0;
                $acertada        = $estado && (int)$estado['es_correcta'] === 1;
                $restantes       = max(0, $max_intentos - $intentos_usados);
                $sin_intentos    = !$acertada && $restantes === 0;
                $bloqueada       = $acertada || $sin_intentos;

                // Ejemplo de respuesta correcta, tal como está cargado en MySQL
                $claves  = array_filter(array_map('trim', explode(',', $item['respuesta_correcta'] ?? '')), 'strlen');
                $ejemplo = implode(', ', $claves);
            ?>
            <div class="card-white-row">
                <p class="question-text"><?= htmlspecialchars($pregunta_mostrar); ?></p>
                <form action="tarea.php" method="POST" class="task-form">
                    <input type="hidden" name="tarea_id" value="<?= $item['id']; ?>">
                    <input type="text" name="respuesta" class="text-input" placeholder="<?= $t['placeholder'] ?>" required autocomplete="off" <?= $bloqueada ? 'disabled' : '' ?>>
                    <div class="status-circle <?= $status['class']; ?>">
                        <?= $status['symbol']; ?>
                    </div>
                    <button type="submit" class="btn-action" <?= $bloqueada ? 'disabled' : '' ?>><?= $t['btn_responder'] ?></button>
                </form>

                <?php if ($acertada): ?>
                    <div class="task-info task-ok"><?= htmlspecialchars($t['acertaste']) ?></div>
                <?php elseif ($sin_intentos): ?>
                    <div class="task-info task-example">
                        <?= htmlspecialchars($t['sin_intentos']) ?>
                        <?= htmlspecialchars($t['ejemplo']) ?> <strong><?= htmlspecialchars($ejemplo) ?></strong>
                    </div>
                <?php else: ?>
                    <div class="task-info"><?= htmlspecialchars($t['intentos_rest']) ?> <?= $restantes ?> / <?= $max_intentos ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </main>
<?php include __DIR__ . '/../staff/tour_include.php'; ?>
</body>
</html>