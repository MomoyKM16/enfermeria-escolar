<?php
// api/solicitudes.php — Persona 2
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';
requerir_login();

$metodo = $_SERVER['REQUEST_METHOD'];

// GET → lista de pedidos (más nuevo primero)
if ($metodo === 'GET') {
    $sql = 'SELECT s.id, s.medicamento_id, s.estudiante_id, s.cantidad, s.estado, s.fecha,
                    m.nombre AS medicamento,
                    e.nombre AS estudiante_nombre, e.apellido AS estudiante_apellido
            FROM solicitudes_medicamento s
            JOIN medicamentos m ON m.id = s.medicamento_id
            JOIN estudiantes e ON e.id = s.estudiante_id
            ORDER BY s.fecha DESC, s.id DESC';
    responder(db()->query($sql)->fetchAll());
}

// POST → crear pedido (queda pendiente)
if ($metodo === 'POST') {
    requerir_rol('enfermera');
    $in   = entrada();
    $medId = $in['medicamento_id'] ?? null;
    $estId = $in['estudiante_id'] ?? null;
    $cant  = $in['cantidad'] ?? null;

    foreach ([$medId, $estId, $cant] as $n) {
        if (!is_numeric($n) || (int)$n != $n || (int)$n <= 0) {
            responder_error('Medicamento, estudiante y cantidad deben ser números mayores que 0');
        }
    }
    $medId = (int)$medId; $estId = (int)$estId; $cant = (int)$cant;

    $st = db()->prepare('SELECT id FROM medicamentos WHERE id = ?');
    $st->execute([$medId]);
    if (!$st->fetch()) {
        responder_error('Medicamento no encontrado', 404);
    }
    $st = db()->prepare('SELECT id FROM estudiantes WHERE id = ?');
    $st->execute([$estId]);
    if (!$st->fetch()) {
        responder_error('Estudiante no encontrado', 404);
    }

    $st = db()->prepare("INSERT INTO solicitudes_medicamento
        (medicamento_id, estudiante_id, cantidad, estado, fecha)
        VALUES (?, ?, ?, 'pendiente', datetime('now'))");
    $st->execute([$medId, $estId, $cant]);
    responder(['id' => (int) db()->lastInsertId()], 201);
}

// PUT ?id= → entregar o rechazar (con transacción)
if ($metodo === 'PUT') {
    requerir_rol('enfermera');
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        responder_error('Falta el id de la solicitud');
    }
    $nuevo = entrada()['estado'] ?? '';
    if (!in_array($nuevo, ['entregado', 'rechazado'], true)) {
        responder_error('Estado no válido');
    }

    $pdo = db();
    $pdo->beginTransaction();
    try {
        $st = $pdo->prepare("SELECT * FROM solicitudes_medicamento WHERE id = ? AND estado = 'pendiente'");
        $st->execute([$id]);
        $sol = $st->fetch();
        if (!$sol) {
            $pdo->rollBack();
            responder_error('La solicitud no existe o ya fue procesada', 404);
        }

        if ($nuevo === 'entregado') {
            $cant  = (int) $sol['cantidad'];
            $medId = (int) $sol['medicamento_id'];
            $st = $pdo->prepare('UPDATE medicamentos SET stock = stock - ? WHERE id = ? AND stock >= ?');
            $st->execute([$cant, $medId, $cant]);
            if ($st->rowCount() === 0) {
                $pdo->rollBack();
                responder_error('No hay stock suficiente');
            }
        }

        $st = $pdo->prepare('UPDATE solicitudes_medicamento SET estado = ? WHERE id = ?');
        $st->execute([$nuevo, $id]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
    responder(['id' => $id, 'estado' => $nuevo]);
}

responder_error('Método no permitido', 405);
