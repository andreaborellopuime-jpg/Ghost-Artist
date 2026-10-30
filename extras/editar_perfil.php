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
        'titulo_pagina'   => 'Editar Perfil',
        'foto_actual'     => 'Foto de Perfil Actual:',
        'sin_foto'        => 'Sin foto cargada',
        'cambiar_foto'    => 'Cambiar Foto:',
        'label_nombre'    => 'Nombre:',
        'label_bio'       => 'Biografía:',
        'placeholder_bio' => 'Escribe una breve descripción...',
        'btn_guardar'     => 'Guardar Cambios',
        'volver_perfil'   => 'Volver al Perfil',
        'exito'           => '¡Perfil actualizado con éxito!',
        'err_db'          => 'Error al actualizar los datos en la base de datos.',
        'err_vacio'       => 'El nombre no puede estar vacío.',
        'err_no_user'     => 'Usuario no encontrado.',
        'cortar_aplicar'  => 'Cortar y Aplicar',
        'cancelar'        => 'Cancelar'
    ],
    'en' => [
        'titulo_pagina'   => 'Edit Profile',
        'foto_actual'     => 'Current Profile Picture:',
        'sin_foto'        => 'No image uploaded',
        'cambiar_foto'    => 'Change Picture:',
        'label_nombre'    => 'Name:',
        'label_bio'       => 'Bio:',
        'placeholder_bio' => 'Write a short description...',
        'btn_guardar'     => 'Save Changes',
        'volver_perfil'   => 'Back to Profile',
        'exito'           => 'Profile updated successfully!',
        'err_db'          => 'Error updating database records.',
        'err_vacio'       => 'Name field cannot be empty.',
        'err_no_user'     => 'User not found.',
        'cortar_aplicar'  => 'Crop & Apply',
        'cancelar'        => 'Cancel'
    ]
];

$t = $textos[$lang];

$usuario_id = $_SESSION['usuario_id'];
$mensaje = '';
$error = '';

$stmt = $pdo->prepare("SELECT nombre, bio, foto_perfil FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die($t['err_no_user']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_nombre = trim($_POST['nombre'] ?? '');
    $nueva_bio = trim($_POST['bio'] ?? '');
    $cropped_image = $_POST['cropped_image'] ?? '';
    $foto_nombre = $user['foto_perfil'];

    if (!empty($nuevo_nombre)) {
        // 1. Si el usuario recortó una nueva imagen (formato Base64)
        if (!empty($cropped_image)) {
            $data = explode(',', $cropped_image);
            if (count($data) === 2) {
                $image_bytes = base64_decode($data[1]);
                $directorio = "../uploads/perfiles/";

                if (!file_exists($directorio)) {
                    mkdir($directorio, 0777, true);
                }

                // Eliminar foto previa si existe
                if (!empty($user['foto_perfil']) && file_exists($directorio . $user['foto_perfil'])) {
                    unlink($directorio . $user['foto_perfil']);
                }

                $foto_nombre = time() . "_" . uniqid() . ".jpg";
                file_put_contents($directorio . $foto_nombre, $image_bytes);
            }
        }

        // 2. Actualizar en Base de Datos
        $updateStmt = $pdo->prepare("UPDATE usuarios SET nombre = ?, bio = ?, foto_perfil = ? WHERE id = ?");
        if ($updateStmt->execute([$nuevo_nombre, $nueva_bio, $foto_nombre, $usuario_id])) {
            $_SESSION['nombre'] = $nuevo_nombre;
            
            $user['nombre'] = $nuevo_nombre;
            $user['bio'] = $nueva_bio;
            $user['foto_perfil'] = $foto_nombre;

            $mensaje = $t['exito'];
        } else {
            $error = $t['err_db'];
        }
    } else {
        $error = $t['err_vacio'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($t['titulo_pagina']) ?></title>
    <link rel="stylesheet" href="../staff/auth.css">
    
    <!-- Cropper.js CDN para el recortador tipo Instagram/TikTok -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    <style>
        .cropper-modal-container {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0,0,0,0.85);
            z-index: 3000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 20px;
            box-sizing: border-box;
        }
        .crop-area {
            max-width: 90%;
            max-height: 65vh;
        }
        .crop-actions {
            margin-top: 15px;
            display: flex;
            gap: 15px;
        }
        .btn-crop-confirm {
            background-color: #2ed573;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-crop-cancel {
            background-color: #ff4757;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        textarea.input-field {
            width: 100%;
            resize: vertical;
            font-family: inherit;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

    <main class="register-container">
        <form action="editar_perfil.php" method="POST" id="formEditarPerfil" class="auth-form">
            
            <!-- Campo oculto para enviar los datos de la imagen recortada en Base64 -->
            <input type="hidden" name="cropped_image" id="croppedImageInput">

            <?php if (!empty($mensaje)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
                    <?= htmlspecialchars($mensaje) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="register-block">
                <label><?= htmlspecialchars($t['foto_actual']) ?></label>
                <div style="text-align: center; margin-bottom: 10px;">
                    <?php if (!empty($user['foto_perfil']) && file_exists("../uploads/perfiles/" . $user['foto_perfil'])): ?>
                        <img id="imgPreview" src="../uploads/perfiles/<?= htmlspecialchars($user['foto_perfil']) ?>" 
                             alt="Foto de perfil" 
                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff;">
                    <?php else: ?>
                        <img id="imgPreview" src="" alt="Vista previa" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid #ffffff; display: none;">
                        <p id="txtSinFoto" style="font-size: 0.9rem; color: #ccc;"><?= htmlspecialchars($t['sin_foto']) ?></p>
                    <?php endif; ?>
                </div>

                <label><?= htmlspecialchars($t['cambiar_foto']) ?></label>
                <input type="file" id="inputFile" accept="image/*" class="input-field" style="padding: 5px;">

                <label><?= htmlspecialchars($t['label_nombre']) ?></label>
                <input type="text" name="nombre" class="input-field" value="<?= htmlspecialchars($user['nombre']) ?>" required>

                <label style="margin-top: 10px; display: block;"><?= htmlspecialchars($t['label_bio']) ?></label>
                <textarea name="bio" rows="3" maxlength="150" class="input-field" placeholder="<?= htmlspecialchars($t['placeholder_bio']) ?>"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 10px;"><?= htmlspecialchars($t['btn_guardar']) ?></button>
            <a href="../staff/usuario.php" style="display: block; text-align: center; color: #ffffff; margin-top: 15px; text-decoration: none; font-size: 0.9rem;"><?= htmlspecialchars($t['volver_perfil']) ?></a><br>
        </form>
    </main>

    <!-- Modal Emergente para Recortar Imagen -->
    <div id="modalCropper" class="cropper-modal-container">
        <div class="crop-area">
            <img id="imageToCrop" src="" style="max-width: 100%;">
        </div>
        <div class="crop-actions">
            <button type="button" class="btn-crop-confirm" onclick="confirmarRecorte()"><?= htmlspecialchars($t['cortar_aplicar']) ?></button>
            <button type="button" class="btn-crop-cancel" onclick="cancelarRecorte()"><?= htmlspecialchars($t['cancelar']) ?></button>
        </div>
    </div>

    <script>
        let cropper;
        const inputFile = document.getElementById('inputFile');
        const modalCropper = document.getElementById('modalCropper');
        const imageToCrop = document.getElementById('imageToCrop');
        const croppedImageInput = document.getElementById('croppedImageInput');
        const imgPreview = document.getElementById('imgPreview');
        const txtSinFoto = document.getElementById('txtSinFoto');

        inputFile.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    imageToCrop.src = event.target.result;
                    modalCropper.style.display = 'flex';

                    if (cropper) {
                        cropper.destroy();
                    }

                    // Iniciar recortador con relación de aspecto 1:1 (cuadrado/círculo)
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1,
                        viewMode: 1,
                        background: false
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        function confirmarRecorte() {
            const canvas = cropper.getCroppedCanvas({
                width: 300,
                height: 300
            });

            const dataUrl = canvas.toDataURL('image/jpeg');
            croppedImageInput.value = dataUrl;

            // Actualizar vista previa antes de guardar
            imgPreview.src = dataUrl;
            imgPreview.style.display = 'inline-block';
            if (txtSinFoto) txtSinFoto.style.display = 'none';

            cancelarRecorte();
        }

        function cancelarRecorte() {
            modalCropper.style.display = 'none';
            if (cropper) {
                cropper.destroy();
            }
            inputFile.value = '';
        }
    </script>

</body>
</html>