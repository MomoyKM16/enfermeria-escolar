<?php
// Devuelve la conexion a SQLite. "static" hace que se abra una sola vez.
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $ruta = __DIR__ . '/../database/enfermeria.sqlite';
        if (!file_exists($ruta)) {
            // Sin esto, PDO crearia una base vacia y los errores serian confusos
            throw new RuntimeException('Falta la base de datos. Corre: php database/instalar.php');
        }
        $pdo = new PDO('sqlite:' . $ruta);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);      // errores como excepciones
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // filas como arreglos con nombre
        $pdo->exec('PRAGMA foreign_keys = ON');  // SQLite no revisa las conexiones entre tablas si no se lo pides
    }
    return $pdo;
}
