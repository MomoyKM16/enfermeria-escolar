<?php
// api/visitas.php — Persona 2
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php' ;
requerir_login();

const ESTADOS_VISITA = ['espera', 'atendido', 'despachado'];

$metodo = $_SERVER['REQUEST_METHOD'];

// GET ?estudiante_id= → historial
if ($metodo === 'GET') {
    $eid = (int)($_GET['estudiante_id'] ?? 0);
    if ($eid <= 0) {
        responder_error('Falta estudiante_id');
    }
    $st = db()->prepare('SELECT * FROM visitas WHERE estudiante_id = ? ORDER BY fecha DESC, id DESC');
    $st->execute([$eid]);
    responder($st->fetchAll());
}

// POST → registrar visita
if ($metodo === 'POST') {
    requerir_rol('enfermera');
    $in     = entrada();
    $eid    = (int)($in['estudiante_id'] ?? 0);
    $motivo = trim((string)($in['motivo'] ?? ''));
    $estado = $in['estado'] ?? 'espera';
    $notas  = trim((string)($in['notas'] ?? '')) ?: null;

    if ($eid <= 0 || $motivo === '') {
        responder_error('El estudiante y el motivo son obligatorios');
    }
    if (!in_array($estado, ESTADOS_VISITA, true)) {
        responder_error('Estado no válido');
    }
    if (mb_strlen($motivo) > 500 || ($notas !== null && mb_strlen($notas) > 2000)) {
        responder_error('El motivo o las notas son demasiado largos');
    }
    $st = db()->prepare('SELECT id FROM estudiantes WHERE id = ?');
    $st->execute([$eid]);
    if (!$st->fetch()) {
        responder_error('Estudiante no encontrado', 404);
    }

    $st = db()->prepare("INSERT INTO visitas (estudiante_id, fecha, motivo, estado, notas, atendido_por)
        VALUES (?, datetime('now'), ?, ?, ?, ?)");
    $st->execute([$eid, $motivo, $estado, $notas, $_SESSION['usuario']['id']]);
    responder(['id' => (int) db()->lastInsertId()], 201);
}

// PUT ?id= → cambiar estado y/o notas
if ($metodo === 'PUT') {
    requerir_rol('enfermera');
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        responder_error('Falta el id de la visita');
    }
    $in = entrada();

    $sets = [];
    $vals = [];
    if (array_key_exists('estado', $in)) {
        if (!in_array($in['estado'], ESTADOS_VISITA, true)) {
            responder_error('Estado no válido');
        }
        $sets[] = 'estado = ?';
        $vals[] = $in['estado'];
    }
    if (array_key_exists('notas', $in)) {
        $notas = trim((string)$in['notas']);
        if (mb_strlen($notas) > 2000) {
            responder_error('Las notas son demasiado largas');
        }
        $sets[] = 'notas = ?';
        $vals[] = $notas === '' ? null : $notas;
    }
    if (!$sets) {
        responder_error('No hay nada que cambiar');
    }

    $st = db()->prepare('SELECT id FROM visitas WHERE id = ?');
    $st->execute([$id]);
    if (!$st->fetch()) {
        responder_error('Visita no encontrada', 404);
    }
    $vals[] = $id;
    $st = db()->prepare('UPDATE visitas SET ' . implode(', ', $sets) . ' WHERE id = ?');
    $st->execute($vals);
    responder(['id' => $id]);
}

responder_error('Método no permitido', 405);
