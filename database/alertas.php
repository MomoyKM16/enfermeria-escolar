<?php
// api/alertas.php — Persona 2
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';
requerir_login();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    responder_error('Método no permitido', 405);
}

// Números propios del sistema (constantes), seguros de escribir directo en el SQL.
// Si fueran parámetros, PHP los enviaría como texto y la comparación fallaría.
$min  = (int) ALERTA_MINIMA;
$dias = (int) DIAS_ALERTA;

$sql = <<<SQL
SELECT e.id, e.nombre, e.apellido, e.curso, e.nombre_padre, e.telefono_padre,
    COUNT(*) AS despachos
FROM visitas v
JOIN estudiantes e ON e.id = v.estudiante_id
WHERE v.estado = 'despachado'
AND v.fecha >= datetime('now', '-{$dias} days')
GROUP BY e.id
HAVING COUNT(*) >= {$min}
ORDER BY despachos DESC, e.apellido
SQL;

$alertas = db()->query($sql)->fetchAll();
foreach ($alertas as &$a) {
    $a['despachos'] = (int) $a['despachos'];
}
unset($a);
responder($alertas);
