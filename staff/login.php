<?php
session_start();
require_once 'database.php'; // Ajustar ruta a tu conexión PDO

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verificar contraseña hasheada o texto plano si es retrocompatible
        if ($user && (password_verify($password, $user['password']) || $password === $user['password'])) {
            $_SESSION['usuario_id'] = $user['id'];
            header("Location: ../index.php");
            exit();
        } else {
            $error = 'Email o contraseña incorrectos';
        }
    } else {
        $error = 'Por favor completa todos los campos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
    <div class="login-mascot">
        <img src="../pics/ghost_wave.webp" alt="" aria-hidden="true">
    </div>

    <main class="login-card">
        <div class="avatar-container"></div>

        <?php if ($error): ?>
            <p style="color: red; font-size: 0.85rem; margin-bottom: 10px;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="login_validar.php" method="POST" class="auth-form">
            <input type="email" name="email" placeholder="email" class="input-field" required>
            <input type="password" name="password" placeholder="contraseña" class="input-field" required>
            
            <button type="submit" class="btn-submit">entrar</button>
            <a href="formulario_login.php" class="btn-secondary">no tengo cuenta</a>
        </form>
    </main>

</body>
</html>