<?php
// staff/tour_include.php
// Tour guiado OBLIGATORIO para cuentas nuevas (recorre varias páginas).
// Se incluye justo antes de </body> en: index, usuario, libro, tarea y desafío.
// Si el usuario ya completó el tour, no imprime nada.

if (!isset($_SESSION['usuario_id'])) { return; }
if (!isset($pdo)) { require_once __DIR__ . '/database.php'; }

$__q = $pdo->prepare("SELECT tour_completado FROM usuarios WHERE id = ?");
$__q->execute([$_SESSION['usuario_id']]);
$__fila = $__q->fetch(PDO::FETCH_ASSOC);
if (!$__fila || (int)$__fila['tour_completado'] === 1) { return; }

$__lang = (($_SESSION['idioma'] ?? 'es') === 'en') ? 'en' : 'es';

// Ruta web de la raíz del proyecto (por ejemplo /GA_proyect)
$__raiz = str_replace('\\', '/', (string)realpath(__DIR__ . '/..'));
$__doc  = str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT']));
$__base = rtrim(substr($__raiz, strlen($__doc)), '/');

$__paginas = [
    'index.php'   => $__base . '/index.php',
    'usuario.php' => $__base . '/staff/usuario.php',
    'libro.php'   => $__base . '/actions/libro.php',
    'tarea.php'   => $__base . '/actions/tarea.php',
    'desafio.php' => $__base . '/actions/desafio.php',
];

// Pasos del recorrido, en orden. 'target' es el elemento que se resalta.
$__pasos_def = [
    ['page' => 'index.php',   'type' => 'welcome', 'target' => null,
        'es' => ['¡Bienvenido/a!', 'Aquí vas a aprender a dibujar con lecciones, pizarrón, tareas y desafíos. Haz un recorrido rápido para ver cómo funciona todo.'],
        'en' => ['Welcome!', 'Here you will learn to draw with lessons, a whiteboard, homework and challenges. Take a quick tour to see how everything works.']],
    ['page' => 'index.php',   'target' => '#menuBtn',
        'es' => ['Menú', 'Desde aquí entras a tu galería, cambias el idioma y accedes a las donaciones.'],
        'en' => ['Menu', 'From here you can open your gallery, change the language and reach the donations page.']],
    ['page' => 'index.php',   'target' => '.card-libro',
        'es' => ['Libro', 'Lecciones paso a paso para aprender a dibujar. Más adelante lo vemos por dentro.'],
        'en' => ['Book', 'Step-by-step lessons to learn how to draw. We will look inside it in a moment.']],
    ['page' => 'index.php',   'target' => '.card-tarea',
        'es' => ['Tarea', 'Preguntas sobre lo que aprendiste. Tienes 3 intentos por pregunta y sumas puntos al acertar.'],
        'en' => ['Homework', 'Questions about what you learned. You get 3 attempts per question and earn points when you get it right.']],
    ['page' => 'index.php',   'target' => '.card-desafio',
        'es' => ['Desafío diario', 'Una consigna de dibujo para practicar. Subes tu obra y sumas puntos.'],
        'en' => ['Daily challenge', 'A drawing prompt to practice. Upload your work and earn points.']],
    ['page' => 'index.php',   'target' => '.profile-btn',
        'es' => ['Tu perfil', 'Tocando tu foto entras a tu perfil. Vamos a visitarlo.'],
        'en' => ['Your profile', 'Tap your picture to open your profile. Let us visit it.']],
    ['page' => 'usuario.php', 'target' => '.level-container',
        'es' => ['Tu nivel', 'Cada 50 puntos subes de nivel. Los puntos los ganas con las tareas y el desafío diario.'],
        'en' => ['Your level', 'Every 50 points you level up. You earn points with homework and the daily challenge.']],
    ['page' => 'usuario.php', 'target' => '.recent-drawings-card',
        'es' => ['Dibujos recientes', 'Aquí aparecen los últimos dibujos que subiste en los desafíos.'],
        'en' => ['Recent drawings', 'The latest drawings you uploaded in the challenges show up here.']],
    ['page' => 'usuario.php', 'target' => '.right-btn',
        'es' => ['Editar perfil', 'Cambia tu foto, tu nombre y tu biografía.'],
        'en' => ['Edit profile', 'Change your picture, your name and your bio.']],
    ['page' => 'libro.php',   'target' => '.panel-libro',
        'es' => ['El libro', 'Pasa las páginas con los botones Anterior y Siguiente.'],
        'en' => ['The book', 'Turn the pages with the Back and Next buttons.']],
    ['page' => 'libro.php',   'target' => '.panel-pizarron',
        'es' => ['Pizarrón', 'Practica lo que lees: tienes pincel, goma, grosor y un botón para limpiar.'],
        'en' => ['Whiteboard', 'Practice what you read: brush, eraser, size and a clear button.']],
    ['page' => 'libro.php',   'target' => '.full-width',
        'es' => ['Videos explicativos', 'Videos paso a paso exclusivos para suscriptores.'],
        'en' => ['Explanatory videos', 'Step-by-step videos available to subscribers.']],
    ['page' => 'tarea.php',   'target' => '.card-white-row',
        'es' => ['Tus tareas', 'Escribe tu respuesta y pulsa responder. El círculo te muestra si acertaste.'],
        'en' => ['Your homework', 'Type your answer and press submit. The circle shows whether you got it right.']],
    ['page' => 'desafio.php', 'target' => '.card-white',
        'es' => ['Desafío diario', 'Lee la consigna, ponle título a tu dibujo, elige la imagen y súbela.'],
        'en' => ['Daily challenge', 'Read the prompt, give your drawing a title, choose the image and upload it.']],
    ['page' => 'desafio.php', 'type' => 'final', 'target' => null,
        'es' => ['¡Todo listo!', 'Ya conoces la plataforma. ¡A dibujar!'],
        'en' => ['All set!', 'You now know the platform. Time to draw!']],
];

$__pasos = [];
foreach ($__pasos_def as $p) {
    $__pasos[] = [
        'page'   => $p['page'],
        'type'   => $p['type'] ?? 'step',
        'target' => $p['target'],
        'title'  => $p[$__lang][0],
        'text'   => $p[$__lang][1],
    ];
}

$__ui = [
    'es' => ['back' => 'Atrás', 'next' => 'Siguiente', 'welcomeLabel' => '¿Primera vez en Ghost Artist?', 'welcomeBtn' => 'Hacer el recorrido', 'finishBtn' => 'Empezar', 'error' => 'No se pudo guardar tu progreso. Inténtalo de nuevo.'],
    'en' => ['back' => 'Back', 'next' => 'Next', 'welcomeLabel' => 'First time at Ghost Artist?', 'welcomeBtn' => 'Take the tour', 'finishBtn' => 'Start', 'error' => 'Could not save your progress. Please try again.'],
][$__lang];

$__json = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
?>
<style>
        .tour-overlay { position: fixed; inset: 0; z-index: 9000; background: transparent; }
        .tour-overlay.dim { background: rgba(10, 8, 25, 0.82); }
        .tour-spot {
            position: fixed; z-index: 9001; display: none; pointer-events: none;
            border-radius: 14px;
            box-shadow: 0 0 0 9999px rgba(10, 8, 25, 0.82), 0 0 0 3px #b8aefc;
            transition: top .25s ease, left .25s ease, width .25s ease, height .25s ease;
        }
        .tour-tip {
            position: fixed; z-index: 9002;
            width: min(340px, calc(100vw - 24px));
            background: #ffffff; color: #2d3436;
            border-radius: 14px; padding: 16px 18px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
        }
        .tour-head { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
        .tour-icon { font-size: 1rem; }
        .tour-title { margin: 0; font-size: 1.05rem; color: #372048; font-weight: 700; }
        .tour-count { margin-left: auto; font-size: 0.72rem; color: #9aa0a6; }
        .tour-label { display: none; font-size: 0.7rem; letter-spacing: 0.08em; text-transform: uppercase; color: #6c5ce7; font-weight: 700; margin-bottom: 10px; }
        .tour-text { margin: 0 0 14px 0; font-size: 0.88rem; line-height: 1.45; color: #4a4a58; }
        .tour-error { margin: 0 0 10px 0; font-size: 0.82rem; color: #c0392b; }
        .tour-actions { display: flex; justify-content: flex-end; gap: 8px; }
        .tour-btn { border-radius: 10px; padding: 8px 16px; font-size: 0.88rem; font-weight: 700; cursor: pointer; border: 1px solid transparent; }
        .tour-back { background: #ffffff; color: #6b7280; border-color: #d1d5db; }
        .tour-next { background: #6c5ce7; color: #ffffff; }
        .tour-btn:hover { filter: brightness(0.95); }
        .tour-btn:disabled { opacity: 0.55; cursor: wait; }

        /* Paso de bienvenida / cierre: tarjeta centrada */
        .tour-tip.is-welcome { width: min(420px, calc(100vw - 24px)); padding: 30px 28px; text-align: center; border-radius: 20px; }
        .is-welcome .tour-label { display: block; }
        .tour-label:empty { display: none !important; }
        .is-welcome .tour-head { flex-direction: column; gap: 12px; margin-bottom: 10px; }
        .is-welcome .tour-icon { display: grid; place-items: center; width: 64px; height: 64px; border-radius: 50%; background: #ede9ff; font-size: 2rem; }
        .is-welcome .tour-title { font-size: 1.6rem; }
        .is-welcome .tour-count { display: none; }
        .is-welcome .tour-text { font-size: 0.95rem; margin-bottom: 20px; }
        .is-welcome .tour-actions { flex-direction: column-reverse; }
        .is-welcome .tour-btn { padding: 13px 16px; font-size: 1rem; }
</style>
<script>
(function () {
    const T = <?= json_encode($__ui, $__json) ?>;
    const STEPS = <?= json_encode($__pasos, $__json) ?>;
    const PAGES = <?= json_encode($__paginas, $__json) ?>;
    const PAGE = <?= json_encode(basename($_SERVER['SCRIPT_NAME'])) ?>;
    const DONE_URL = <?= json_encode($__base . '/actions/tour_completado.php', $__json) ?>;
    const HOME = <?= json_encode($__paginas['index.php'], $__json) ?>;
    const KEY = 'ga_tour_step';
    const N = STEPS.length;

    let i = 0;
    try { i = parseInt(sessionStorage.getItem(KEY), 10) || 0; } catch (e) {}
    if (i < 0 || i >= N) i = 0;
    function saveStep() { try { sessionStorage.setItem(KEY, String(i)); } catch (e) {} }

    // ---- DOM ----
    const overlay = document.createElement('div');
    overlay.className = 'tour-overlay dim';
    const spot = document.createElement('div');
    spot.className = 'tour-spot';
    const tip = document.createElement('div');
    tip.className = 'tour-tip';
    tip.setAttribute('role', 'dialog');
    tip.setAttribute('aria-modal', 'true');
    tip.innerHTML =
        '<div class="tour-label"></div>' +
        '<div class="tour-head">' +
            '<span class="tour-icon" aria-hidden="true">\uD83D\uDC7B</span>' +
            '<h3 class="tour-title"></h3>' +
            '<span class="tour-count"></span>' +
        '</div>' +
        '<p class="tour-text"></p>' +
        '<p class="tour-error" hidden></p>' +
        '<div class="tour-actions">' +
            '<button type="button" class="tour-btn tour-back"></button>' +
            '<button type="button" class="tour-btn tour-next"></button>' +
        '</div>';
    document.body.append(overlay, spot, tip);

    const label = tip.querySelector('.tour-label');
    const title = tip.querySelector('.tour-title');
    const count = tip.querySelector('.tour-count');
    const text  = tip.querySelector('.tour-text');
    const errBox = tip.querySelector('.tour-error');
    const back = tip.querySelector('.tour-back');
    const next = tip.querySelector('.tour-next');

    // El tour es obligatorio: si el paso actual es de otra página, se va hacia ella
    if (STEPS[i].page !== PAGE) {
        tip.style.display = 'none';
        location.replace(PAGES[STEPS[i].page]);
        return;
    }

    // El velo bloquea clics y scroll del usuario
    overlay.addEventListener('wheel', e => e.preventDefault(), { passive: false });
    overlay.addEventListener('touchmove', e => e.preventDefault(), { passive: false });

    function place(scroll) {
        const s = STEPS[i];
        const el = s.target ? document.querySelector(s.target) : null;
        const tw = tip.offsetWidth, th = tip.offsetHeight;
        const vw = window.innerWidth, vh = window.innerHeight;

        if (!el) {
            spot.style.display = 'none';
            overlay.classList.add('dim');
            tip.style.top = Math.max(12, (vh - th) / 2) + 'px';
            tip.style.left = Math.max(12, (vw - tw) / 2) + 'px';
            return;
        }

        overlay.classList.remove('dim');
        if (scroll) el.scrollIntoView({ block: 'center', inline: 'nearest', behavior: 'instant' });

        const r = el.getBoundingClientRect();
        const pad = 8;
        spot.style.display = 'block';
        spot.style.top = (r.top - pad) + 'px';
        spot.style.left = (r.left - pad) + 'px';
        spot.style.width = (r.width + pad * 2) + 'px';
        spot.style.height = (r.height + pad * 2) + 'px';

        // Globo debajo del elemento; si no cabe, arriba; si tampoco, al pie de la pantalla
        let top = r.bottom + pad + 12;
        if (top + th > vh - 12) top = r.top - pad - 12 - th;
        if (top < 12) top = Math.max(12, vh - th - 12);
        let left = r.left + r.width / 2 - tw / 2;
        left = Math.max(12, Math.min(left, vw - tw - 12));
        tip.style.top = top + 'px';
        tip.style.left = left + 'px';
    }

    function show() {
        const s = STEPS[i];
        const centered = (s.type === 'welcome' || s.type === 'final');
        tip.classList.toggle('is-welcome', centered);
        label.textContent = (s.type === 'welcome') ? T.welcomeLabel : '';
        title.textContent = s.title;
        text.textContent = s.text;
        count.textContent = (i >= 1 && s.type !== 'final') ? (i + ' / ' + (N - 2)) : '';
        back.hidden = (i <= 1);
        back.textContent = '\u2190 ' + T.back;
        next.textContent = (s.type === 'welcome') ? T.welcomeBtn
                         : (s.type === 'final')   ? T.finishBtn
                         : (T.next + ' \u2192');
        errBox.hidden = true;
        place(true);
        next.focus({ preventScroll: true });
    }

    function go(d) {
        const j = i + d;
        if (j < 0 || j >= N) return;
        i = j;
        saveStep();
        if (STEPS[i].page !== PAGE) {
            location.href = PAGES[STEPS[i].page];   // el recorrido continúa en la otra página
        } else {
            show();
        }
    }

    async function finish() {
        next.disabled = true;
        try {
            const res = await fetch(DONE_URL, { method: 'POST', credentials: 'same-origin' });
            const data = await res.json();
            if (!data.ok) throw new Error('no ok');
            try { sessionStorage.removeItem(KEY); } catch (e) {}
            cleanup();
            location.href = HOME;
        } catch (e) {
            errBox.textContent = T.error;
            errBox.hidden = false;
            next.disabled = false;
        }
    }

    function onKey(e) {
        if (e.key === 'Escape') { e.preventDefault(); return; }
        if (e.key === 'Tab') {
            const btns = [back, next].filter(b => !b.hidden && !b.disabled);
            e.preventDefault();
            if (!btns.length) return;
            const idx = btns.indexOf(document.activeElement);
            const nextIdx = e.shiftKey ? (idx <= 0 ? btns.length - 1 : idx - 1)
                                       : (idx === btns.length - 1 ? 0 : idx + 1);
            btns[nextIdx].focus();
        }
    }
    const onReflow = () => place(false);

    function cleanup() {
        document.removeEventListener('keydown', onKey, true);
        window.removeEventListener('resize', onReflow);
        window.removeEventListener('scroll', onReflow);
        overlay.remove(); spot.remove(); tip.remove();
    }

    back.addEventListener('click', () => go(-1));
    next.addEventListener('click', () => { if (i === N - 1) { finish(); } else { go(1); } });
    document.addEventListener('keydown', onKey, true);
    window.addEventListener('resize', onReflow);
    window.addEventListener('scroll', onReflow, { passive: true });

    show();
})();
</script>