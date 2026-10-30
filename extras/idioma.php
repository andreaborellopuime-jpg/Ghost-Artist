<?php
session_start();

// 1. Si se envía una selección de idioma por URL o POST, se actualiza la sesión
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, ['es', 'en'])) {
        $_SESSION['idioma'] = $lang;
    }
}

// Idioma actual por defecto (Español si no se ha elegido otro)
$idioma_actual = $_SESSION['idioma'] ?? 'es';

// Procesar envío del formulario si se usa POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idioma'])) {
    $nuevo_idioma = $_POST['idioma'];
    if (in_array($nuevo_idioma, ['es', 'en'])) {
        $_SESSION['idioma'] = $nuevo_idioma;
        $idioma_actual = $nuevo_idioma;
        
        // Redirigir a la página anterior o al inicio
        $referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
        header("Location: " . $referer);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($idioma_actual) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $idioma_actual === 'es' ? 'Seleccionar Idioma' : 'Select Language' ?></title>
    <link rel="stylesheet" href="../staff/auth.css">
</head>
<body>

    <header class="header-top">
    </header>

    <main class="register-container">
        <form action="idioma.php" method="POST" class="auth-form">
            
            <div class="register-block">
                <label><?= $idioma_actual === 'es' ? 'Seleccionar Idioma / Select Language' : 'Select Language / Seleccionar Idioma' ?></label>
                
                <div class="radio-group" style="flex-direction: column; gap: 15px; margin-top: 10px;">
                    <label class="radio-btn" style="justify-content: flex-start; padding: 12px; font-size: 1rem;">
                        <input type="radio" name="idioma" value="es" <?= $idioma_actual === 'es' ? 'checked' : '' ?>>
                        <span>🇪🇸 Español (Spanish)</span>
                    </label>

                    <label class="radio-btn" style="justify-content: flex-start; padding: 12px; font-size: 1rem;">
                        <input type="radio" name="idioma" value="en" <?= $idioma_actual === 'en' ? 'checked' : '' ?>>
                        <span>🇬🇧 English (Inglés)</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit" style="margin-top: 10px;">
                <?= $idioma_actual === 'es' ? 'Guardar Cambios' : 'Save Changes' ?>
            </button>
            
            <a href="../index.php" style="display: block; text-align: center; color: #ffffff; margin-top: 15px; text-decoration: none; font-size: 0.9rem;">
                <?= $idioma_actual === 'es' ? 'Volver al Inicio' : 'Back to Home' ?>
            </a>
        </form>
    </main>

</body>
</html>