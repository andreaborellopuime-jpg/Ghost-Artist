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
        'titulo_pagina'  => 'Apoyar el proyecto',
        'volver'         => 'Volver',
        'subtitulo'      => '¿Te gusta la plataforma?',
        'descripcion'    => 'Tus donaciones nos ayudan a mantener los servidores activos y seguir agregando más contenido interactivo.',
        'monto_label'    => 'Selecciona o ingresa un monto:',
        'monto_custom'   => 'Otro monto',
        'metodo_label'   => 'Elige un método de pago:',
        'btn_mp'         => 'Donar con Mercado Pago',
        'btn_paypal'     => 'Donar con PayPal',
        'gracias'        => '¡Gracias por tu apoyo!'
    ],
    'en' => [
        'titulo_pagina'  => 'Support the project',
        'volver'         => 'Back',
        'subtitulo'      => 'Enjoying the platform?',
        'descripcion'    => 'Your donations help us keep the servers running and continue adding more interactive content.',
        'monto_label'    => 'Select or enter an amount:',
        'monto_custom'   => 'Custom amount',
        'metodo_label'   => 'Choose a payment method:',
        'btn_mp'         => 'Donate with Mercado Pago',
        'btn_paypal'     => 'Donate with PayPal',
        'gracias'        => 'Thank you for your support!'
    ]
];

$t = $textos[$lang];
$usuario_id = $_SESSION['usuario_id'];

// Obtener datos del usuario para el header
$stmt_user = $pdo->prepare("SELECT foto_perfil FROM usuarios WHERE id = ?");
$stmt_user->execute([$usuario_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);
$foto_perfil = $user['foto_perfil'] ?? null;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['titulo_pagina'] ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .donation-card {
            background-color: #2a2845;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
        }

        .donation-card h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .donation-card p {
            font-size: 0.95rem;
            line-height: 1.4;
            color: #d1d1e0;
            margin-bottom: 20px;
        }

        .amounts-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .amount-btn {
            background-color: #3f3961;
            color: #ffffff;
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .amount-btn:hover, .amount-btn.active {
            border-color: #9cb043;
            background-color: #4a4373;
        }

        .custom-amount-input {
            width: 100%;
            background-color: #3f3961;
            border: none;
            border-radius: 10px;
            padding: 12px;
            color: #ffffff;
            font-size: 1rem;
            text-align: center;
            margin-bottom: 20px;
            outline: none;
        }

        .payment-btns {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .btn-payment {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: bold;
            text-decoration: none;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn-payment:hover {
            transform: translateY(-2px);
        }

        .btn-mp {
            background-color: #009ee3;
            color: #ffffff;
        }

        .btn-paypal {
            background-color: #003087;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <header class="top-bar">
        <a href="../index.php" class="nav-btn left-btn" title="<?= $t['volver'] ?>">&lt;</a>
        <h1 class="page-title"><?= $t['titulo_pagina'] ?></h1>
        <a href="../staff/usuario.php" class="profile-btn">
            <?php if (!empty($foto_perfil) && file_exists("../uploads/perfiles/" . $foto_perfil)): ?>
                <img src="../uploads/perfiles/<?= htmlspecialchars($foto_perfil) ?>" class="avatar-icon" style="object-fit: cover; border-radius: 50%;">
            <?php else: ?>
                <div class="avatar-icon"></div>
            <?php endif; ?>
        </a>
    </header>

    <main class="content-container">
        <div class="donation-card">
            <h2><?= $t['subtitulo'] ?></h2>
            <p><?= $t['descripcion'] ?></p>

            <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold;"><?= $t['monto_label'] ?></label>
            
            <div class="amounts-grid">
                <button type="button" class="amount-btn" onclick="selectAmount(5, this)">$5</button>
                <button type="button" class="amount-btn active" onclick="selectAmount(10, this)">$10</button>
                <button type="button" class="amount-btn" onclick="selectAmount(25, this)">$25</button>
            </div>

            <input type="number" id="customAmount" class="custom-amount-input" placeholder="<?= $t['monto_custom'] ?>" value="10" min="1">

            <label style="display: block; text-align: left; margin-bottom: 8px; font-weight: bold;"><?= $t['metodo_label'] ?></label>
            
            <div class="payment-btns">
                <button type="button" class="btn-payment btn-mp" onclick="procesarPago('mercadopago')">
                    <?= $t['btn_mp'] ?>
                </button>
                <button type="button" class="btn-payment btn-paypal" onclick="procesarPago('paypal')">
                    <?= $t['btn_paypal'] ?>
                </button>
            </div>
        </div>
    </main>

    <script>
        function selectAmount(value, element) {
            document.querySelectorAll('.amount-btn').forEach(btn => btn.classList.remove('active'));
            element.classList.add('active');
            document.getElementById('customAmount').value = value;
        }

        function procesarPago(plataforma) {
            const monto = document.getElementById('customAmount').value;
            if (!monto || monto <= 0) {
                alert('Por favor ingresa un monto válido.');
                return;
            }

            // Aquí puedes redirigir a tu pasarela de pago o webhook real
            if (plataforma === 'mercadopago') {
                alert('Redirigiendo a Mercado Pago por $' + monto + '...');
                // window.location.href = 'link_mercadopago.php?monto=' + monto;
            } else if (plataforma === 'paypal') {
                alert('Redirigiendo a PayPal por $' + monto + '...');
                // window.location.href = 'link_paypal.php?monto=' + monto;
            }
        }
    </script>

</body>
</html>