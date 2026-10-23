<?php
// api/estudiantes.php — Persona 2
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';
requerir_login();

$metodo = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$dias   = (int) DIAS_ALERTA; // número propio del sistema, seguro de escribir en el SQL

// Valida y limpia los datos de un estudiante (sirve para POST y PUT)
function datos_estudiante(array $in): array {
    $d = [
        'nombre'         => trim((string)($in['nombre'] ?? '')),
        'apellido'       => trim((string)($in['apellido'] ?? '')),
        'curso'          => trim((string)($in['curso'] ?? '')),
        'matricula'      => trim((string)($in['matricula'] ?? '')) ?: null,
        'nombre_padre'   => trim((string)($in['nombre_padre'] ?? '')) ?: null,
        'telefono_padre' => trim((string)($in['telefono_padre'] ?? '')) ?: null,
    ];
    if ($d['nombre'] === '' || $d['apellido'] === '' || $d['curso'] === '') {
        responder_error('Nombre, apellido y curso son obligatorios');
    }
    foreach ($d as $campo => $valor) {
        if ($valor !== null && mb_strlen($valor) > 100) {
            responder_error("El campo $campo es demasiado largo");
        }
    }
    if ($d['telefono_padre'] !== null && !preg_match('/^[0-9 +\-]{7,15}$/', $d['telefono_padre'])) {
        responder_error('El teléfono no es válido');
    }
    return $d;
}

// 1) GET ?id= → un estudiante con su historial
if ($metodo === 'GET' && $id > 0) {
    $st = db()->prepare('SELECT * FROM estudiantes WHERE id = ?');
    $st->execute([$id]);
    $est = $st->fetch();
    if (!$est) {
        responder_error('Estudiante no encontrado', 404);
    }
    $st = db()->prepare('SELECT * FROM visitas WHERE estudiante_id = ? ORDER BY fecha DESC, id DESC');
    $st->execute([$id]);
    $est['visitas'] = $st->fetchAll();
    responder($est);
}

// 2) GET sin id → listar y buscar
if ($metodo === 'GET') {
    $q     = trim((string)($_GET['q'] ?? ''));
    $curso = trim((string)($_GET['curso'] ?? ''));

    $sql = <<<SQL
SELECT e.id, e.matricula, e.nombre, e.apellido, e.curso,
  (SELECT v.estado FROM visitas v WHERE v.estudiante_id = e.id
     ORDER BY v.fecha DESC, v.id DESC LIMIT 1) AS estado_actual,
  (SELECT COUNT(*) FROM visitas v WHERE v.estudiante_id = e.id
     AND v.estado = 'despachado'
     AND v.fecha >= datetime('now', '-{$dias} days')) AS despachos_recientes
FROM estudiantes e
WHERE 1 = 1
SQL;
    $params = [];
    if ($q !== '') {
        $sql .= ' AND (e.nombre LIKE :q1 OR e.apellido LIKE :q2 OR e.matricula LIKE :q3)';
        $params[':q1'] = $params[':q2'] = $params[':q3'] = '%' . $q . '%';
    }
    if ($curso !== '') {
        $sql .= ' AND e.curso = :curso';
        $params[':curso'] = $curso;
    }
    $sql .= ' ORDER BY e.apellido, e.nombre';

    $st = db()->prepare($sql);
    $st->execute($params);
    $lista = $st->fetchAll();
    foreach ($lista as &$fila) {
        $fila['despachos_recientes'] = (int) $fila['despachos_recientes'];
    }
    unset($fila);
    responder($lista);
}

// 3) POST → crear estudiante
if ($metodo === 'POST') {
    requerir_rol('enfermera');
    $d = datos_estudiante(entrada());
    try {
        $st = db()->prepare('INSERT INTO estudiantes
            (matricula, nombre, apellido, curso, nombre_padre, telefono_padre)
            VALUES (:matricula, :nombre, :apellido, :curso, :nombre_padre, :telefono_padre)');
        $st->execute($d);
    } catch (PDOException $e) {
        if (stripos($e->getMessage(), 'UNIQUE') !== false) {
            responder_error('Esa matrícula ya existe');
        }
        throw $e;
    }
    responder(['id' => (int) db()->lastInsertId()], 201);
}

// 4) PUT ?id= → editar estudiante
if ($metodo === 'PUT') {
    requerir_rol('enfermera');
    if ($id <= 0) {
        responder_error('Falta el id del estudiante');
    }
    $st = db()->prepare('SELECT id FROM estudiantes WHERE id = ?');
    $st->execute([$id]);
    if (!$st->fetch()) {
        responder_error('Estudiante no encontrado', 404);
    }
    $d = datos_estudiante(entrada());
    $d['id'] = $id;
    try {
        $st = db()->prepare('UPDATE estudiantes SET matricula = :matricula, nombre = :nombre,
            apellido = :apellido, curso = :curso, nombre_padre = :nombre_padre,
            telefono_padre = :telefono_padre WHERE id = :id');
        $st->execute($d);
    } catch (PDOException $e) {
        if (stripos($e->getMessage(), 'UNIQUE') !== false) {
            responder_error('Esa matrícula ya existe');
        }
        throw $e;
    }
    responder(['id' => $id]);
}

responder_error('Método no permitido', 405);
