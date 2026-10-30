<?php
// actions/tour_completado.php
// Marca el tour como completado para el usuario con sesión iniciada.
session_start();
require_once '../staff/database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['ok' => false]);
    exit();
}

$pdo->prepare("UPDATE usuarios SET tour_completado = 1 WHERE id = ?")
    ->execute([$_SESSION['usuario_id']]);

echo json_encode(['ok' => true]);