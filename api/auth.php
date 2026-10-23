<?php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';

$accion = $_GET['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

// POST api/auth.php?accion=login   cuerpo: {"usuario": "...", "clave": "..."}
if ($accion === 'login' && $metodo === 'POST') {
    $d = entrada();
    $usuario = trim($d['usuario'] ?? '');
    $clave   = $d['clave'] ?? '';

    // "?" en vez de pegar el texto en el SQL: asi nadie puede colar codigo malicioso
    $st = db()->prepare('SELECT * FROM usuarios WHERE usuario = ?');
    $st->execute([$usuario]);
    $u = $st->fetch();

    // Mismo mensaje si falla el usuario o la clave (no dar pistas).
    // Codigo 400 y NO 401: si fuera 401, api.js creeria que la sesion vencio y recargaria el login sin parar.
    if (!$u || !password_verify($clave, $u['clave_hash'])) {
        responder_error('Usuario o clave incorrectos');
    }

    session_regenerate_id(true);  // nuevo id de sesion tras entrar
    // Se guarda solo id, nombre y rol. NUNCA la clave.
    $_SESSION['usuario'] = ['id' => (int) $u['id'], 'nombre' => $u['nombre'], 'rol' => $u['rol']];
    responder($_SESSION['usuario']);
}

// GET api/auth.php?accion=sesion   -> quien soy y mi rol
if ($accion === 'sesion' && $metodo === 'GET') {
    requerir_login();
    responder($_SESSION['usuario']);
}

// POST api/auth.php?accion=logout
if ($accion === 'logout' && $metodo === 'POST') {
    requerir_login();
    $_SESSION = [];
    session_destroy();
    responder(true);
}

responder_error('Pedido no encontrado', 404);
