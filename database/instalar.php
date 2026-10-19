<?php
// Arma la base de datos local de cada persona:  php database/instalar.php
// Se puede correr las veces que quieras (borra y recrea todo).

$ruta = __DIR__ . '/enfermeria.sqlite';

// 1. Borrar la base vieja si existe
if (file_exists($ruta)) {
    if (!@unlink($ruta)) {
        exit("No pude borrar enfermeria.sqlite. Apaga el servidor (Ctrl+C) y cierra DB Browser, luego intenta de nuevo.\n");
    }
}

// 2. Conectarse (SQLite crea el archivo solo)
$pdo = new PDO('sqlite:' . $ruta);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON');

// 3. Crear tablas y llenar datos de prueba
$pdo->exec(file_get_contents(__DIR__ . '/schema.sql'));
$pdo->exec(file_get_contents(__DIR__ . '/seed.sql'));

// 4. Usuarios de prueba (la clave se guarda "revuelta", nunca tal cual)
$usuarios = [
    ['Yajaira',     'yajaira',     'enfermera123', 'enfermera'],
    ['Orientación', 'orientacion', 'orienta123',   'orientacion'],
];
$st = $pdo->prepare('INSERT INTO usuarios (nombre, usuario, clave_hash, rol) VALUES (?, ?, ?, ?)');
foreach ($usuarios as [$nombre, $usuario, $clave, $rol]) {
    $st->execute([$nombre, $usuario, password_hash($clave, PASSWORD_DEFAULT), $rol]);
}

// 5. Mensaje de exito
echo "Base de datos creada en database/enfermeria.sqlite\n";
echo "Usuarios de prueba: yajaira / enfermera123  y  orientacion / orienta123\n";
