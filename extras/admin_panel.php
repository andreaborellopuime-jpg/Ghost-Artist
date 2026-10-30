<?php
session_start();
require_once '../staff/database.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$lang =$_SESSION['idioma'] ?? 'es';

$textos = [
    'es' => [
        'titulo_pagina'       => 'Panel Admin',
        'gestion_tareas'      => 'Gestión de Tareas',
        'crear_nueva_tarea'   => 'Crear Nueva Tarea',
        'opcion_crear_tarea'  => '-- Crear Nueva Tarea --',
        'select_accion'       => 'Seleccionar Acción / Pregunta:',
        'label_pregunta_es'   => 'Pregunta (Español):',
        'label_pregunta_en'   => 'Pregunta (Inglés):',
        'label_respuesta'     => 'Respuesta Correcta:',
        'btn_crear_tarea'     => 'Crear Tarea',
        'btn_guardar_cambios' => 'Guardar Cambios (#',
        'titulo_editar_tarea' => 'Editar Tarea #',
        'desafio_seccion'     => 'Editar Consigna de Desafío Diario',
        'label_consigna_es'   => 'Consigna (Español):',
        'label_consigna_en'   => 'Consigna (Inglés):',
        'btn_guardar_desafio' => 'Guardar Desafío',
        'msg_tarea_creada'    => '¡Nueva tarea creada con éxito!',
        'msg_tarea_upd'       => '¡Tarea #%d actualizada con éxito!',
        'msg_desafio_upd'     => '¡Desafío Diario guardado correctamente en la Base de Datos!',
        'tarea_prefix'        => 'Tarea #',
        'seccion_libro'       => 'Gestión de Páginas del Libro',
        'agregar_pagina'      => 'Agregar Nueva Página',
        'label_img_es'        => 'Imagen (Español):',
        'label_img_en'        => 'Imagen (Inglés):',
        'btn_guardar_pagina'  => 'Guardar Página',
        'reordenar_paginas'   => 'Ordenar Páginas',
        'subir'               => '▲ Subir',
        'bajar'               => '▼ Bajar',
        'eliminar'            => 'Eliminar',
        'msg_pagina_creada'   => '¡Página del libro agregada con éxito!',
        'msg_orden_upd'       => '¡Orden de páginas actualizado!',
        'msg_pagina_del'      => '¡Página eliminada con éxito!',
        'paginas_existentes'  => 'Páginas existentes',
        'sin_paginas'         => 'Aún no hay páginas cargadas.',
        'pagina_num'          => 'Página',
        'reemplazar_titulo'   => 'Cambiar imágenes',
        'btn_reemplazar'      => 'Reemplazar',
        'nueva_img_es'        => 'Nueva imagen (Español):',
        'nueva_img_en'        => 'Nueva imagen (Inglés):',
        'msg_pagina_upd'      => '¡Página actualizada con éxito!',
        'msg_pagina_err'      => 'Error: sube una imagen válida (JPG, PNG, GIF o WebP).',
        'sin_cambios'         => 'Elige al menos una imagen nueva para reemplazar.',
        'confirmar_borrar'    => '¿Eliminar esta página?',
        'select_pagina'       => 'Seleccionar Página:',
        'opcion_nueva_pagina' => '-- Agregar Nueva Página --',
        'titulo_editar_pagina'=> 'Editar Página #',
        'imagen_actual'       => 'Imagen actual',
        'dejar_vacio'         => 'Déjalo vacío para conservar la actual.',
        'seccion_videos'      => 'Gestión de Videos Explicativos',
        'agregar_video'       => 'Agregar Nuevo Video',
        'label_titulo_vid'    => 'Título del Video:',
        'label_archivo_vid'   => 'Seleccionar Archivo de Video (MP4, WebM, Ogg):',
        'btn_guardar_video'   => 'Subir y Guardar Video',
        'videos_cargados'     => 'Videos Cargados',
        'msg_video_creado'    => '¡Video subido y guardado con éxito!',
        'msg_video_err'       => 'Error al subir el archivo de video.',
        'msg_video_del'       => '¡Video eliminado con éxito!'
    ],
    'en' => [
        'titulo_pagina'       => 'Admin Panel',
        'gestion_tareas'      => 'Task Management',
        'crear_nueva_tarea'   => 'Create New Task',
        'opcion_crear_tarea'  => '-- Create New Task --',
        'select_accion'       => 'Select Action / Question:',
        'label_pregunta_es'   => 'Question (Spanish):',
        'label_pregunta_en'   => 'Question (English):',
        'label_respuesta'     => 'Correct Answer:',
        'btn_crear_tarea'     => 'Create Task',
        'btn_guardar_cambios' => 'Save Changes (#',
        'titulo_editar_tarea' => 'Edit Task #',
        'desafio_seccion'     => 'Edit Daily Challenge Prompt',
        'label_consigna_es'   => 'Prompt (Spanish):',
        'label_consigna_en'   => 'Prompt (English):',
        'btn_guardar_desafio' => 'Save Challenge',
        'msg_tarea_creada'    => 'New task created successfully!',
        'msg_tarea_upd'       => 'Task #%d updated successfully!',
        'msg_desafio_upd'     => 'Daily Challenge successfully saved in the Database!',
        'tarea_prefix'        => 'Task #',
        'seccion_libro'       => 'Book Pages Management',
        'agregar_pagina'      => 'Add New Page',
        'label_img_es'        => 'Image (Spanish):',
        'label_img_en'        => 'Image (English):',
        'btn_guardar_pagina'  => 'Save Page',
        'reordenar_paginas'   => 'Reorder Pages',
        'subir'               => '▲ Move Up',
        'bajar'               => '▼ Move Down',
        'eliminar'            => 'Delete',
        'msg_pagina_creada'   => 'Book page added successfully!',
        'msg_orden_upd'       => 'Page order updated!',
        'msg_pagina_del'      => 'Page deleted successfully!',
        'paginas_existentes'  => 'Existing pages',
        'sin_paginas'         => 'No pages uploaded yet.',
        'pagina_num'          => 'Page',
        'reemplazar_titulo'   => 'Change images',
        'btn_reemplazar'      => 'Replace',
        'nueva_img_es'        => 'New image (Spanish):',
        'nueva_img_en'        => 'New image (English):',
        'msg_pagina_upd'      => 'Page updated successfully!',
        'msg_pagina_err'      => 'Error: upload a valid image (JPG, PNG, GIF or WebP).',
        'sin_cambios'         => 'Choose at least one new image to replace.',
        'confirmar_borrar'    => 'Delete this page?',
        'select_pagina'       => 'Select Page:',
        'opcion_nueva_pagina' => '-- Add New Page --',
        'titulo_editar_pagina'=> 'Edit Page #',
        'imagen_actual'       => 'Current image',
        'dejar_vacio'         => 'Leave empty to keep the current one.',
        'seccion_videos'      => 'Explanatory Videos Management',
        'agregar_video'       => 'Add New Video',
        'label_titulo_vid'    => 'Video Title:',
        'label_archivo_vid'   => 'Select Video File (MP4, WebM, Ogg):',
        'btn_guardar_video'   => 'Upload & Save Video',
        'videos_cargados'     => 'Uploaded Videos',
        'msg_video_creado'    => 'Video uploaded and saved successfully!',
        'msg_video_err'       => 'Error uploading the video file.',
        'msg_video_del'       => 'Video deleted successfully!'
    ]
];

$t =$textos[$lang];$mensaje = "";

// Helper para subir imágenes
function subirImagen($fileInputName) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return '';
        }
        $dir = '../uploads/libro/';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        $nombreFinal = 'libro_' . uniqid() . '.' . $ext;
        if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $dir . $nombreFinal)) {
            return $dir . $nombreFinal;
        }
    }
    return '';
}

// Helper para borrar una imagen del libro (solo dentro de uploads/libro)
function borrarImagenLibro($ruta) {
    if (empty($ruta)) return;
    $archivo = '../uploads/libro/' . basename($ruta);
    if (is_file($archivo)) {
        @unlink($archivo);
    }
}

// Helper para subir archivos de video
function subirVideo($fileInputName) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES[$fileInputName]['name'], PATHINFO_EXTENSION));$extensionesPermitidas = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
        if (in_array($ext,$extensionesPermitidas)) {
            if (!file_exists('../uploads/videos')) {
                mkdir('../uploads/videos', 0777, true);
            }
            $nombreFinal = 'video_' . uniqid() . '.' . $ext;
            $destino = '../uploads/videos/' .$nombreFinal;
            if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'],$destino)) {
                return '../uploads/videos/' . $nombreFinal;
            }
        }
    }
    return false;
}

// 1. Guardar o Crear Tareas (Español e Inglés)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_tarea'])) {
    $id_tarea           = (int)($_POST['id_tarea'] ?? 0);
    $pregunta_es        = trim($_POST['pregunta_es'] ?? '');
    $pregunta_en        = trim($_POST['pregunta_en'] ?? '');
    $respuesta_correcta = trim($_POST['respuesta_correcta'] ?? '');

    if ($id_tarea === 0) {
        try {
            $stmt =$pdo->prepare("INSERT INTO tareas (pregunta_es, pregunta_en, pregunta, respuesta_correcta) VALUES (?, ?, ?, ?)");
            $stmt->execute([$pregunta_es,$pregunta_en, $pregunta_es,$respuesta_correcta]);
            $mensaje =$t['msg_tarea_creada'];
        } catch (PDOException $e) {
            $stmt =$pdo->prepare("INSERT INTO tareas (pregunta, respuesta_correcta) VALUES (?, ?)");
            $stmt->execute([$pregunta_es,$respuesta_correcta]);
            $mensaje =$t['msg_tarea_creada'];
        }
    } else {
        try {
            $stmt =$pdo->prepare("UPDATE tareas SET pregunta_es = ?, pregunta_en = ?, pregunta = ?, respuesta_correcta = ? WHERE id = ?");
            $stmt->execute([$pregunta_es,$pregunta_en, $pregunta_es,$respuesta_correcta, $id_tarea]);$mensaje = sprintf($t['msg_tarea_upd'],$id_tarea);
        } catch (PDOException $e) {
            $stmt =$pdo->prepare("UPDATE tareas SET pregunta = ?, respuesta_correcta = ? WHERE id = ?");
            $stmt->execute([$pregunta_es,$respuesta_correcta, $id_tarea]);$mensaje = sprintf($t['msg_tarea_upd'],$id_tarea);
        }
    }
}

// 2. Guardar Desafío Diario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_desafio'])) {
    $descripcion_es = trim($_POST['descripcion_es'] ?? '');
    $descripcion_en = trim($_POST['descripcion_en'] ?? '');

    $check =$pdo->query("SELECT id FROM desafios WHERE id = 1")->fetch();

    if ($check) {
        try {
            $stmt =$pdo->prepare("UPDATE desafios SET descripcion = ?, descripcion_es = ?, descripcion_en = ? WHERE id = 1");
            $stmt->execute([$descripcion_es, $descripcion_es,$descripcion_en]);
        } catch (PDOException $e) {
            $stmt =$pdo->prepare("UPDATE desafios SET descripcion = ? WHERE id = 1");
            $stmt->execute([$descripcion_es]);
        }
    } else {
        try {
            $stmt =$pdo->prepare("INSERT INTO desafios (id, titulo, descripcion, descripcion_es, descripcion_en) VALUES (1, 'Desafío Diario', ?, ?, ?)");
            $stmt->execute([$descripcion_es, $descripcion_es,$descripcion_en]);
        } catch (PDOException $e) {
            $stmt =$pdo->prepare("INSERT INTO desafios (id, titulo, descripcion) VALUES (1, 'Desafío Diario', ?)");
            $stmt->execute([$descripcion_es]);
        }
    }
    $mensaje =$t['msg_desafio_upd'];
}

// 3. Guardar página del libro: id 0 = crear, id > 0 = editar (igual que las tareas)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_guardar_pagina'])) {
    $id_sel = (int)($_POST['id_pagina_libro'] ?? 0);
    if ($id_sel === 0) {
        $_POST['action_agregar_pagina'] = 1;
    } else {
        $_POST['action_reemplazar_pagina'] = 1;
        $_POST['id_pagina'] = $id_sel;
    }
}

// 3a. Crear Nueva Página para el Libro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_agregar_pagina'])) {
    $imagen_es = subirImagen('imagen_es');
    $imagen_en = subirImagen('imagen_en');

    if ($imagen_es === '') {
        $mensaje = $t['msg_pagina_err'];
        borrarImagenLibro($imagen_en);
    } else {
        $stmtMax = $pdo->query("SELECT MAX(orden) as max_orden FROM libro_paginas");
        $max = $stmtMax ? $stmtMax->fetch(PDO::FETCH_ASSOC)['max_orden'] : 0;
        $nuevo_orden = ($max !== null) ? $max + 1 : 1;

        $stmt = $pdo->prepare("INSERT INTO libro_paginas (orden, imagen_es, imagen_en) VALUES (?, ?, ?)");
        $stmt->execute([$nuevo_orden, $imagen_es, $imagen_en]);

        $mensaje = $t['msg_pagina_creada'];
    }
}

// 3b. Reemplazar las imágenes de una página existente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_reemplazar_pagina'])) {
    $id_pagina = (int)($_POST['id_pagina'] ?? 0);

    $stmt = $pdo->prepare("SELECT imagen_es, imagen_en FROM libro_paginas WHERE id = ?");
    $stmt->execute([$id_pagina]);
    $actual = $stmt->fetch(PDO::FETCH_ASSOC);

    $subioEs = isset($_FILES['imagen_es']) && $_FILES['imagen_es']['error'] !== UPLOAD_ERR_NO_FILE;
    $subioEn = isset($_FILES['imagen_en']) && $_FILES['imagen_en']['error'] !== UPLOAD_ERR_NO_FILE;

    if (!$actual) {
        $mensaje = $t['msg_pagina_err'];
    } elseif (!$subioEs && !$subioEn) {
        $mensaje = $t['sin_cambios'];
    } else {
        $nuevaEs = $subioEs ? subirImagen('imagen_es') : '';
        $nuevaEn = $subioEn ? subirImagen('imagen_en') : '';

        if (($subioEs && $nuevaEs === '') || ($subioEn && $nuevaEn === '')) {
            // Alguna subida falló: no se toca nada y se limpia lo que sí se subió
            borrarImagenLibro($nuevaEs);
            borrarImagenLibro($nuevaEn);
            $mensaje = $t['msg_pagina_err'];
        } else {
            $imgEs = $nuevaEs !== '' ? $nuevaEs : $actual['imagen_es'];
            $imgEn = $nuevaEn !== '' ? $nuevaEn : $actual['imagen_en'];

            $pdo->prepare("UPDATE libro_paginas SET imagen_es = ?, imagen_en = ? WHERE id = ?")
                ->execute([$imgEs, $imgEn, $id_pagina]);

            // Borrar los archivos viejos que fueron reemplazados
            if ($nuevaEs !== '') borrarImagenLibro($actual['imagen_es']);
            if ($nuevaEn !== '') borrarImagenLibro($actual['imagen_en']);

            $mensaje = $t['msg_pagina_upd'];
        }
    }
}

// 4. Mover Página o Eliminar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_orden_pagina'])) {
    $id_pagina = (int)$_POST['id_pagina'];
    $direccion =$_POST['direccion'];

    if ($direccion === 'eliminar') {
        $stmtImg = $pdo->prepare("SELECT imagen_es, imagen_en FROM libro_paginas WHERE id = ?");
        $stmtImg->execute([$id_pagina]);
        if ($imgs = $stmtImg->fetch(PDO::FETCH_ASSOC)) {
            borrarImagenLibro($imgs['imagen_es']);
            borrarImagenLibro($imgs['imagen_en']);
        }
        $stmt =$pdo->prepare("DELETE FROM libro_paginas WHERE id = ?");
        $stmt->execute([$id_pagina]);
        $mensaje =$t['msg_pagina_del'];
    } else {
        $stmt =$pdo->prepare("SELECT id, orden FROM libro_paginas WHERE id = ?");
        $stmt->execute([$id_pagina]);
        $paginaActual =$stmt->fetch(PDO::FETCH_ASSOC);

        if ($paginaActual) {
            $ordenActual =$paginaActual['orden'];
            $operador = ($direccion === 'up') ? '<' : '>';
            $orderDirection = ($direccion === 'up') ? 'DESC' : 'ASC';

            $stmtVecino =$pdo->prepare("SELECT id, orden FROM libro_paginas WHERE orden {$operador} ? ORDER BY orden {$orderDirection} LIMIT 1");
            $stmtVecino->execute([$ordenActual]);
            $paginaVecina =$stmtVecino->fetch(PDO::FETCH_ASSOC);

            if ($paginaVecina) {
                $pdo->prepare("UPDATE libro_paginas SET orden = ? WHERE id = ?")->execute([$paginaVecina['orden'], $paginaActual['id']]);$pdo->prepare("UPDATE libro_paginas SET orden = ? WHERE id = ?")->execute([$ordenActual,$paginaVecina['id']]);
                $mensaje =$t['msg_orden_upd'];
            }
        }
    }
}

// 5. Cargar o Eliminar Videos Explicativos por Archivo Directo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_agregar_video'])) {$titulo_video = trim($_POST['titulo_video'] ?? '');$ruta_video   = subirVideo('archivo_video');

    if (!empty($titulo_video) && $ruta_video) {
        $stmtVid =$pdo->prepare("INSERT INTO videos_explicativos (titulo, url) VALUES (?, ?)");
        $stmtVid->execute([$titulo_video,$ruta_video]);
        $mensaje =$t['msg_video_creado'];
    } else {
        $mensaje =$t['msg_video_err'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_eliminar_video'])) {
    $id_video = (int)$_POST['id_video'];
    
    // Consultar para borrar el archivo físico del servidor
    $stmtGetVid =$pdo->prepare("SELECT url FROM videos_explicativos WHERE id = ?");
    $stmtGetVid->execute([$id_video]);
    $vidData =$stmtGetVid->fetch(PDO::FETCH_ASSOC);
    if ($vidData && !empty($vidData['url']) && file_exists($vidData['url'])) {
        unlink($vidData['url']);
    }

    $stmtVidDel =$pdo->prepare("DELETE FROM videos_explicativos WHERE id = ?");
    $stmtVidDel->execute([$id_video]);
    $mensaje =$t['msg_video_del'];
}

// Cargar tareas existentes
$listaTareas =$pdo->query("SELECT * FROM tareas ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// Cargar desafío actual
$stmtDesafio =$pdo->query("SELECT * FROM desafios WHERE id = 1");
$desafioActual = $stmtDesafio ? $stmtDesafio->fetch(PDO::FETCH_ASSOC) : null;

// Cargar páginas del libro ordenadas
$paginasLibro =$pdo->query("SELECT * FROM libro_paginas ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);

// Cargar videos explicativos
$listaVideos =$pdo->query("SELECT * FROM videos_explicativos ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['titulo_pagina']) ?></title>
    <link rel="stylesheet" href="../style.css">

<style>
    .admin-container { 
        max-width: 1100px; 
        margin: 67px auto 44px; 
        background: linear-gradient(135deg, #2a2845, #3f3961); 
        padding: 25px; 
        border-radius: 15px; 
        color: #fff; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5); 
        transition: transform 0.3s ease;
    }
    
    .admin-container:hover {
        transform: translateY(-5px);
    }

    /* Layout Grid en 2 columnas */
    .admin-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .admin-section { 
        background: rgba(63, 57, 97, 0.95); 
        padding: 20px; 
        border-radius: 10px; 
        margin-bottom: 0; 
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .admin-section:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 20px rgba(0, 206, 201, 0.25);
    }

    .form-group { display: flex; flex-direction: column; gap: 6px; margin-bottom: 12px; }
    .form-group input, .form-group textarea, .form-group select { 
        background-color: #2a2845; 
        border: 1px solid #5a5286; 
        border-radius: 6px; 
        padding: 8px; 
        color: #fff; 
        transition: border-color 0.3s ease;
    }
    
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
        border-color: #6c5ce7;
        box-shadow: 0 0 5px rgba(108, 92, 231, 0.4);
    }
    
    .btn-save { 
        background: linear-gradient(135deg, var(--lime-action), #009473);
    color: var(--text-light);
    text-decoration: none;
    padding: 10px 30px;
    border-radius: 30px;
    font-weight: 700;
    font-size: 1.05rem;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0, 184, 148, 0.4);
    transition: all 0.3s var(--transition-smooth);
    display: inline-block;
    }
    
    .btn-save:hover {
        background: linear-gradient(135deg, var(--lime-hover), var(--lime-action));
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 8px 22px rgba(85, 239, 196, 0.5);
    color: var(--purple-dark);
    }
    
    /* Páginas del libro */
    .pag-thumbs { display: flex; gap: 10px; margin-bottom: 12px; }
    .pag-thumbs figure { margin: 0; flex: 1; text-align: center; font-size: 0.8rem; }
    .pag-thumbs img { width: 100%; height: 130px; object-fit: contain; background: #fff; border-radius: 6px; }
    .pag-acciones { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }
    .btn-mini { background: #5a5286; color: #fff; border: none; border-radius: 6px; padding: 8px 12px; cursor: pointer; }
    .btn-mini:hover { background: #6c5ce7; }
    .btn-mini.peligro { background: #c0392b; }

    .msg { 
        background: #00cec9; 
        color: #080808; 
        padding: 10px; 
        border-radius: 6px; 
        text-align: center; 
        margin-bottom: 20px; 
    }
    
    .page-list-item { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        background: #2a2845; 
        padding: 10px; 
        border-radius: 6px; 
        margin-bottom: 8px; 
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .page-list-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    }
    
    .page-preview-img { 
        width: 45px; 
        height: 45px; 
        object-fit: cover; 
        border-radius: 4px; 
        border: 1px solid #5a5286; 
    }
    
    .btn-action { 
        background: #5a5286; 
        color: #fff; 
        border: none; 
        padding: 5px 10px; 
        border-radius: 4px; 
        cursor: pointer; 
        font-size: 0.85rem; 
        transition: background 0.3s ease;
    }

    .btn-action:hover {
        background: #6c5ce7;
    }

    .btn-delete { 
        background: #a83232; 
    }

    .page-actions { 
        display: flex; 
        gap: 5px; 
    }

    .page-info { 
        display: flex; 
        align-items: center; 
        gap: 10px; 
    }

    /* Adaptación para pantallas pequeñas */
    @media (max-width: 850px) {
        .admin-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body>
    <header class="top-bar">
        <a href="../index.php" class="nav-btn left-btn">&lt;</a>
        <h1 class="page-title"><?= htmlspecialchars($t['titulo_pagina']) ?></h1>
    </header>

    <main class="admin-container">
        <?php if ($mensaje): ?><div class="msg"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>

        <div class="admin-grid">
            <!-- BLOQUE 1: GESTIÓN DE TAREAS -->
            <section class="admin-section">
                <div>
                    <h2 id="titulo_formulario"><?= htmlspecialchars($t['gestion_tareas']) ?></h2>
                    <form method="POST" id="form_tarea">
                        <input type="hidden" name="action_tarea" value="1">
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['select_accion']) ?></label>
                            <select name="id_tarea" id="select_tarea" onchange="cargarTarea()">
                                <option value="0" data-es="" data-en="" data-resp=""><?= htmlspecialchars($t['opcion_crear_tarea']) ?></option>
                                <?php foreach ($listaTareas as$tarea): ?>
                                    <option value="<?= $tarea['id'] ?>" 
                                            data-es="<?= htmlspecialchars($tarea['pregunta_es'] ?? $tarea['pregunta'] ?? '') ?>"
                                            data-en="<?= htmlspecialchars($tarea['pregunta_en'] ?? '') ?>"
                                            data-resp="<?= htmlspecialchars($tarea['respuesta_correcta'] ?? '') ?>">
                                        <?= htmlspecialchars($t['tarea_prefix']) ?><?= $tarea['id'] ?>: <?= htmlspecialchars(substr(($lang === 'en' && !empty($tarea['pregunta_en'])) ?$tarea['pregunta_en'] : ($tarea['pregunta_es'] ?? $tarea['pregunta'] ?? ''), 0, 30)) ?>...
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_pregunta_es']) ?></label>
                            <textarea name="pregunta_es" id="tarea_es" rows="2" required></textarea>
                        </div>
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_pregunta_en']) ?></label>
                            <textarea name="pregunta_en" id="tarea_en" rows="2" required></textarea>
                        </div>
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_respuesta']) ?></label>
                            <input type="text" name="respuesta_correcta" id="tarea_resp" required>
                        </div>
                    </form>
                </div>
                <button type="submit" form="form_tarea" class="btn-save" id="btn_guardar_tarea"><?= htmlspecialchars($t['btn_crear_tarea']) ?></button>
            </section>

            <!-- BLOQUE 2: DESAFÍO DIARIO -->
            <section class="admin-section">
                <div>
                    <h2><?= htmlspecialchars($t['desafio_seccion']) ?></h2>
                    <form method="POST" id="form_desafio">
                        <input type="hidden" name="action_desafio" value="1">
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_consigna_es']) ?></label>
                            <textarea name="descripcion_es" rows="3" required><?= htmlspecialchars($desafioActual['descripcion_es'] ?? $desafioActual['descripcion'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_consigna_en']) ?></label>
                            <textarea name="descripcion_en" rows="3"><?= htmlspecialchars($desafioActual['descripcion_en'] ?? '') ?></textarea>
                        </div>
                    </form>
                </div>
                <button type="submit" form="form_desafio" class="btn-save"><?= htmlspecialchars($t['btn_guardar_desafio']) ?></button>
            </section>

            <!-- BLOQUE 3: PÁGINAS DEL LIBRO (mismo esquema que las tareas) -->
            <section class="admin-section">
                <div>
                    <h2 id="titulo_form_libro"><?= htmlspecialchars($t['seccion_libro']) ?></h2>
                    <form method="POST" enctype="multipart/form-data" id="form_libro">
                        <input type="hidden" name="action_guardar_pagina" value="1">
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['select_pagina']) ?></label>
                            <select name="id_pagina_libro" id="select_pagina" onchange="cargarPagina()">
                                <option value="0" data-es="" data-en=""><?= htmlspecialchars($t['opcion_nueva_pagina']) ?></option>
                                <?php foreach ($paginasLibro as $n => $pg): ?>
                                    <option value="<?= (int)$pg['id'] ?>"
                                            data-es="<?= !empty($pg['imagen_es']) ? 'ver_libro.php?f=' . urlencode(basename($pg['imagen_es'])) : '' ?>"
                                            data-en="<?= !empty($pg['imagen_en']) ? 'ver_libro.php?f=' . urlencode(basename($pg['imagen_en'])) : '' ?>">
                                        <?= htmlspecialchars($t['pagina_num']) ?> <?= $n + 1 ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="pag-thumbs" id="pag_previews" style="display:none;">
                            <figure>
                                <img id="prev_es" src="" alt="ES">
                                <figcaption>ES - <?= htmlspecialchars($t['imagen_actual']) ?></figcaption>
                            </figure>
                            <figure>
                                <img id="prev_en" src="" alt="EN">
                                <figcaption>EN - <?= htmlspecialchars($t['imagen_actual']) ?></figcaption>
                            </figure>
                        </div>

                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_img_es']) ?></label>
                            <input type="file" name="imagen_es" id="file_es" accept="image/*" required>
                        </div>
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_img_en']) ?></label>
                            <input type="file" name="imagen_en" id="file_en" accept="image/*">
                        </div>
                        <small id="hint_libro" style="display:none; opacity:.8;"><?= htmlspecialchars($t['dejar_vacio']) ?></small>
                    </form>

                    <!-- Formulario aparte (no se puede anidar) para ordenar y eliminar -->
                    <form method="POST" id="form_orden_pagina">
                        <input type="hidden" name="action_orden_pagina" value="1">
                        <input type="hidden" name="id_pagina" id="orden_id_pagina" value="0">
                    </form>
                </div>

                <div class="pag-acciones">
                    <button type="submit" form="form_libro" class="btn-save" id="btn_guardar_pagina"><?= htmlspecialchars($t['btn_guardar_pagina']) ?></button>
                    <span id="acciones_pagina" style="display:none;">
                        <button type="submit" form="form_orden_pagina" name="direccion" value="up" class="btn-mini"><?= htmlspecialchars($t['subir']) ?></button>
                        <button type="submit" form="form_orden_pagina" name="direccion" value="down" class="btn-mini"><?= htmlspecialchars($t['bajar']) ?></button>
                        <button type="submit" form="form_orden_pagina" name="direccion" value="eliminar" class="btn-mini peligro"
                                onclick="return confirm(TXT.confirmarBorrar)"><?= htmlspecialchars($t['eliminar']) ?></button>
                    </span>
                </div>
            </section>

            <!-- BLOQUE 4: VIDEOS EXPLICATIVOS -->
            <section class="admin-section">
                <div>
                    <h2><?= htmlspecialchars($t['seccion_videos']) ?></h2>
                    <h3><?= htmlspecialchars($t['agregar_video']) ?></h3>
                    <form method="POST" enctype="multipart/form-data" id="form_videos">
                        <input type="hidden" name="action_agregar_video" value="1">
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_titulo_vid']) ?></label>
                            <input type="text" name="titulo_video" required>
                        </div>
                        <div class="form-group">
                            <label><?= htmlspecialchars($t['label_archivo_vid']) ?></label>
                            <input type="file" name="archivo_video" accept="video/mp4,video/webm,video/ogg,video/quicktime,video/x-msvideo" required>
                        </div>
                    </form>
                </div>
                <button type="submit" form="form_videos" class="btn-save"><?= htmlspecialchars($t['btn_guardar_video']) ?></button>
            </section>
        </div>
    </main>

    <script>
        const TXT = {
            gestionTareas:  <?= json_encode($t['gestion_tareas']) ?>,
            btnCrearTarea:  <?= json_encode($t['btn_crear_tarea']) ?>,
            editarTarea:    <?= json_encode($t['titulo_editar_tarea']) ?>,
            guardarCambios: <?= json_encode($t['btn_guardar_cambios']) ?>,
            seccionLibro:   <?= json_encode($t['seccion_libro']) ?>,
            btnGuardarPag:  <?= json_encode($t['btn_guardar_pagina']) ?>,
            editarPagina:   <?= json_encode($t['titulo_editar_pagina']) ?>,
            confirmarBorrar:<?= json_encode($t['confirmar_borrar']) ?>
        };

        function cargarTarea() {
            const select = document.getElementById('select_tarea');
            const selectedOption = select.options[select.selectedIndex];

            const id = select.value;
            document.getElementById('tarea_es').value = selectedOption.getAttribute('data-es') || '';
            document.getElementById('tarea_en').value = selectedOption.getAttribute('data-en') || '';
            document.getElementById('tarea_resp').value = selectedOption.getAttribute('data-resp') || '';

            const btn = document.getElementById('btn_guardar_tarea');
            const titulo = document.getElementById('titulo_formulario');

            if (id === '0') {
                btn.textContent = TXT.btnCrearTarea;
                titulo.textContent = TXT.gestionTareas;
            } else {
                btn.textContent = TXT.guardarCambios + id + ')';
                titulo.textContent = TXT.editarTarea + id;
            }
        }

        function cargarPagina() {
            const select = document.getElementById('select_pagina');
            const opt = select.options[select.selectedIndex];
            const id = select.value;
            const nueva = (id === '0');

            const es = opt.getAttribute('data-es') || '';
            const en = opt.getAttribute('data-en') || '';
            document.getElementById('prev_es').src = es;
            document.getElementById('prev_en').src = en;
            document.getElementById('pag_previews').style.display = nueva ? 'none' : 'flex';

            // Limpiar archivos elegidos al cambiar de página
            document.getElementById('file_es').value = '';
            document.getElementById('file_en').value = '';
            // Al crear, la imagen en español es obligatoria; al editar es opcional
            document.getElementById('file_es').required = nueva;
            document.getElementById('hint_libro').style.display = nueva ? 'none' : 'block';

            document.getElementById('acciones_pagina').style.display = nueva ? 'none' : 'inline';
            document.getElementById('orden_id_pagina').value = id;

            document.getElementById('btn_guardar_pagina').textContent = nueva ? TXT.btnGuardarPag : (TXT.guardarCambios + id + ')');
            document.getElementById('titulo_form_libro').textContent = nueva ? TXT.seccionLibro : (TXT.editarPagina + id);
        }
    </script>
</body>
</html>