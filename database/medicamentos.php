<?php
// api/medicamentos.php — Persona 2 (solo lectura)
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido', 405);
}

$lista = db()->query('SELECT id, nombre, stock FROM medicamentos ORDER BY nombre')->fetchAll();
responder($lista);
