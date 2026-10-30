<?php
session_start();
require_once 'database.php';

// Idioma por defecto o lectura del parámetro GET / SESIÓN
$lang = $_GET['lang'] ?? $_SESSION['idioma'] ?? 'es';
$_SESSION['idioma'] = $lang;

$textos = [
    'es' => [
        'titulo_pagina' => 'Registro',
        'email'         => 'Email:',
        'password'      => 'Contraseña:',
        'verificar'     => 'Verificar:',
        'foto_perfil'   => 'Foto de Perfil:',
        'nombre'        => 'Nombre:',
        'edad'          => 'Edad:',
        'experiencia'   => 'Experiencia:',
        'novato'        => 'novato',
        'intermedio'    => 'intermedio',
        'avanzado'      => 'avanzado',
        'pais'          => 'País:',
        'idioma'        => 'Idioma:',
        'seleccionar'   => 'seleccionar',
        'lang_es'       => 'Español',
        'lang_en'       => 'Inglés',
        'btn_registrar' => 'Registrarse'
    ],
    'en' => [
        'titulo_pagina' => 'Register',
        'email'         => 'Email:',
        'password'      => 'Password:',
        'verificar'     => 'Confirm Password:',
        'foto_perfil'   => 'Profile Picture:',
        'nombre'        => 'Name:',
        'edad'          => 'Age:',
        'experiencia'   => 'Experience:',
        'novato'        => 'beginner',
        'intermedio'    => 'intermediate',
        'avanzado'      => 'advanced',
        'pais'          => 'Country:',
        'idioma'        => 'Language:',
        'seleccionar'   => 'select',
        'lang_es'       => 'Spanish',
        'lang_en'       => 'English',
        'btn_registrar' => 'Sign Up'
    ]
];

$t = $textos[$lang];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $verificar = $_POST['verificar'] ?? '';
    $nombre    = trim($_POST['nombre'] ?? '');
    $edad      = filter_input(INPUT_POST, 'edad', FILTER_VALIDATE_INT) ?: null;
    $nivel     = $_POST['experiencia'] ?? 'novato';
    $pais      = trim($_POST['pais'] ?? '');
    $idioma_sel= trim($_POST['idioma'] ?? 'es');
    $foto_nombre = null;

    // Guardar preferencia de idioma seleccionada en la sesión activa
    $_SESSION['idioma'] = ($idioma_sel === 'en' || $idioma_sel === 'Inglés') ? 'en' : 'es';

    if ($password === $verificar && !empty($email) && !empty($password)) {
        
        if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
            $directorio = "../uploads/perfiles/";
            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }
            $ext = pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION);
            $foto_nombre = time() . "_" . uniqid() . "." . $ext;
            move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $directorio . $foto_nombre);
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO usuarios (email, password, nombre, edad, nivel, pais, idioma, foto_perfil) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$email, $password_hash, $nombre, $edad, $nivel, $pais, $idioma_sel, $foto_nombre])) {
            $_SESSION['usuario_id'] = $pdo->lastInsertId();
            $_SESSION['nombre']     = $nombre;
            
            header("Location: ../index.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['titulo_pagina'] ?></title>
    <link rel="stylesheet" href="auth.css?v=2">
</head>
<body>

    <header class="header-top">
    </header>

    <main class="register-container">
        <form action="formulario_login.php?lang=<?= htmlspecialchars($lang) ?>" method="POST" enctype="multipart/form-data" class="auth-form">
            
            <!-- Bloque 1: Credenciales -->
            <div class="register-block">
                <label><?= $t['email'] ?></label>
                <input type="email" name="email" class="input-field" required>
                
                <label><?= $t['password'] ?></label>
                <input type="password" name="password" class="input-field" required>
                
                <label><?= $t['verificar'] ?></label>
                <input type="password" name="verificar" class="input-field" required>
            </div>

            <!-- Bloque 2: Perfil y Foto -->
            <div class="register-block">
                <label><?= $t['foto_perfil'] ?></label>
                <input type="file" name="foto_perfil" accept="image/*" class="input-field" style="padding: 5px;">

                <label><?= $t['nombre'] ?></label>
                <input type="text" name="nombre" class="input-field" required>
                
                <label><?= $t['edad'] ?></label>
                <input type="number" name="edad" class="input-field">
                
                <label><?= $t['experiencia'] ?></label>
                <div class="radio-group">
                    <label class="radio-btn"><input type="radio" name="experiencia" value="novato" checked> <?= $t['novato'] ?></label>
                    <label class="radio-btn"><input type="radio" name="experiencia" value="intermedio"> <?= $t['intermedio'] ?></label>
                    <label class="radio-btn"><input type="radio" name="experiencia" value="avanzado"> <?= $t['avanzado'] ?></label>
                </div>
            </div>

            <!-- Bloque 3: Ubicación e Idioma -->
            <div class="register-block">
                <label><?= $t['pais'] ?></label>
                <select name="pais" class="select-field">
                    <option value=""><?= $t['seleccionar'] ?></option>
                    <option value="Argentina">Argentina</option>
                    <option value="México">México</option>
                    <option value="España">España</option>
                    <option value="USA">USA</option>
                </select>

                <label><?= $t['idioma'] ?></label>
                <select name="idioma" id="select-idioma" class="select-field" onchange="cambiarIdiomaFormulario(this.value)">
                    <option value="es" <?= $lang === 'es' ? 'selected' : '' ?>><?= $t['lang_es'] ?></option>
                    <option value="en" <?= $lang === 'en' ? 'selected' : '' ?>><?= $t['lang_en'] ?></option>
                </select>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 10px;"><?= $t['btn_registrar'] ?></button>
        </form>
    </main>

    <script>
        function cambiarIdiomaFormulario(idioma) {
            window.location.href = 'formulario_login.php?lang=' + idioma;
        }
    </script>

</body>
</html>