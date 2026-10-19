<?php
// Ayudantes que usan todos los archivos de api/.
session_start();
header('Content-Type: application/json; charset=utf-8');

// Reglas de la alerta (cambiar aqui si el profesor confirma otro periodo)
const ALERTA_MINIMA = 4;   // despachos necesarios para entrar en alerta
const DIAS_ALERTA   = 30;  // en cuantos dias se cuentan

// Responde {"ok": true, "data": ...} y termina
function responder($data, int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

// Responde {"ok": false, "error": "..."} y termina
function responder_error(string $mensaje, int $codigo = 400): void
{
    http_response_code($codigo);
    echo json_encode(['ok' => false, 'error' => $mensaje], JSON_UNESCAPED_UNICODE);
    exit;
}

// Lee el JSON que mando el navegador y lo devuelve como arreglo
function entrada(): array
{
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);
    return is_array($datos) ? $datos : [];
}

// Si no hay sesion: error 401
function requerir_login(): void
{
    if (empty($_SESSION['usuario'])) {
        responder_error('Debes iniciar sesión', 401);
    }
}

// Si el rol no coincide: error 403 (esto es lo que de verdad protege los datos)
function requerir_rol(string $rol): void
{
    requerir_login();
    if ($_SESSION['usuario']['rol'] !== $rol) {
        responder_error('No tienes permiso para hacer esto', 403);
    }
}

// Cualquier error de base de datos sale como JSON y no como pantalla rota.
// ANTES DE LA ENTREGA FINAL: quitar ' . $e->getMessage()' para no mostrar detalles internos.
set_exception_handler(function (Throwable $e) {
    responder_error('Error del servidor: ' . $e->getMessage(), 500);
});
