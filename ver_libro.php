<?php
// actions/ver_libro.php
// Entrega las imágenes del libro (uploads/libro está bloqueada por .htaccess).
// - Admin: imagen original.
// - Resto de usuarios con sesión: imagen con marca de agua.

session_start();

// No imprimir errores dentro de la imagen; se guardan en el log de Apache
ini_set('display_errors', '0');
ini_set('memory_limit', '512M');

function fallar($codigo, $detalle = '') {
    if ($detalle !== '') error_log('ver_libro.php: ' . $detalle);
    http_response_code($codigo);
    header('Content-Type: text/plain; charset=utf-8');
    exit($detalle !== '' ? "Error $codigo: $detalle" : '');
}

if (!isset($_SESSION['usuario_id'])) fallar(403);

$usuario_id = (int)$_SESSION['usuario_id'];
$es_admin   = (($_SESSION['rol'] ?? '') === 'admin');

// Solo el nombre del archivo (evita ../../)
$archivo = basename($_GET['f'] ?? '');
$ruta    = __DIR__ . '/../uploads/libro/' . $archivo;
$ext     = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

$tipos = [
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png',  'gif'  => 'image/gif', 'webp' => 'image/webp'
];
if (!isset($tipos[$ext]) || !is_file($ruta)) fallar(404, "No existe el archivo: $archivo");

// Evita que cualquier salida previa (espacios, BOM) corrompa la imagen
while (ob_get_level() > 0) ob_end_clean();

// ---- Admin: original sin tocar ----
if ($es_admin) {
    header('Content-Type: ' . $tipos[$ext]);
    header('Content-Length: ' . filesize($ruta));
    header('Cache-Control: private, no-cache');
    readfile($ruta);
    exit;
}

// ---- Resto de usuarios: marca de agua (con respaldo si falla GD) ----
if (!extension_loaded('gd')) {
    // Si no hay GD, entregamos el archivo original directamente para no romper la vista
    header('Content-Type: ' . $tipos[$ext]);
    header('Content-Length: ' . filesize($ruta));
    header('Cache-Control: private, no-store');
    readfile($ruta);
    exit;
}

$datos = file_get_contents($ruta);
$img   = $datos !== false ? @imagecreatefromstring($datos) : false;
unset($datos);

// SI GD FALLA (por imagen muy pesada, memoria o formato complejo):
// En lugar de romper la imagen, servimos el archivo original directamente.
if (!$img) {
    header('Content-Type: ' . $tipos[$ext]);
    header('Content-Length: ' . filesize($ruta));
    header('Cache-Control: private, no-store');
    readfile($ruta);
    exit;
}

// Reducir imágenes muy grandes (control de errores en redimensionamiento)
$w = imagesx($img);
$h = imagesy($img);
$maxAncho = 1600;

if ($w && $h && $w > $maxAncho) {
    $nuevoH = (int)round($h * $maxAncho / $w);
    $redim  = @imagecreatetruecolor($maxAncho, $nuevoH);
    if ($redim) {
        imagecopyresampled($redim, $img, 0, 0, 0, 0, $maxAncho, $nuevoH, $w, $h);
        imagedestroy($img);
        $img = $redim;
        $w = $maxAncho;
        $h = $nuevoH;
    }
}
imagealphablending($img, true);

$texto  = 'ghost artist';
$fuente = 'C:/Windows/Fonts/arial.ttf';
$usarTTF = function_exists('imagettftext') && is_file($fuente);

// Gris semitransparente (0 = opaco, 127 = invisible)
$color = imagecolorallocatealpha($img, 135, 135, 135, 95);

if ($usarTTF) {
    $tam    = max(14, (int)($w / 40));
    $angulo = 30;
    $pasoX  = $tam * 12;
    $pasoY  = $tam * 6;
    for ($y = $pasoY; $y < $h + $pasoX; $y += $pasoY) {
        for ($x = -$pasoX; $x < $w; $x += $pasoX) {
            @imagettftext($img, $tam, $angulo, $x, $y, $color, $fuente, $texto);
        }
    }
} else {
    // Plan B sin fuente TTF
    for ($y = 20; $y < $h; $y += 90) {
        for ($x = 10; $x < $w; $x += 260) {
            @imagestring($img, 5, $x, $y, $texto, $color);
        }
    }
}

// Cada usuario recibe una imagen distinta: no se guarda en caché
header('Cache-Control: private, no-store');
if ($ext === 'jpg' || $ext === 'jpeg') {
    header('Content-Type: image/jpeg');
    @imagejpeg($img, null, 90);
} elseif ($ext === 'webp' && function_exists('imagewebp')) {
    header('Content-Type: image/webp');
    @imagewebp($img, null, 90);
} else {
    header('Content-Type: image/png');
    @imagepng($img);
}
if ($img) {
    imagedestroy($img);
}
exit;