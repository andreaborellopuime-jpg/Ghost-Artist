<?php
session_start();
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        header("Location: login.php?error=campos_vacios");
        exit();
    }

    // Consulta para obtener las credenciales y estado del usuario
    $stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($password, $usuario['password'])) {
        // Guardar datos en la sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['rol']        = $usuario['rol'] ?? 'usuario';
        $_SESSION['nombre']     = $usuario['nombre'];

        // Redirigir al panel de usuario
        header("Location: usuario.php");
        exit();
    } else {
        header("Location: login.php?error=datos_incorrectos");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>