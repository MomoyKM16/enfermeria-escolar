-- Esquema de la base de datos. Cada CREATE TABLE es una "hoja de Excel".
-- Las tablas que otras necesitan van primero.
-- Los nombres de columnas son parte del contrato: P2, P3 y P4 dependen de ellos.

PRAGMA foreign_keys = ON;

CREATE TABLE usuarios (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre      TEXT NOT NULL,
    usuario     TEXT NOT NULL UNIQUE,
    clave_hash  TEXT NOT NULL,
    rol         TEXT NOT NULL CHECK (rol IN ('enfermera','orientacion'))
);

CREATE TABLE estudiantes (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    matricula       TEXT UNIQUE,            -- opcional; NULL se permite varias veces
    nombre          TEXT NOT NULL,
    apellido        TEXT NOT NULL,
    curso           TEXT NOT NULL,
    nombre_padre    TEXT,
    telefono_padre  TEXT,
    creado_en       TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE visitas (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    estudiante_id  INTEGER NOT NULL REFERENCES estudiantes(id),
    fecha          TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
    motivo         TEXT NOT NULL,
    estado         TEXT NOT NULL DEFAULT 'espera'
                   CHECK (estado IN ('espera','atendido','despachado')),
    notas          TEXT,
    atendido_por   INTEGER REFERENCES usuarios(id)
);

CREATE TABLE medicamentos (
    id      INTEGER PRIMARY KEY AUTOINCREMENT,
    nombre  TEXT NOT NULL UNIQUE,
    stock   INTEGER NOT NULL DEFAULT 0 CHECK (stock >= 0)
);

CREATE TABLE solicitudes_medicamento (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    medicamento_id  INTEGER NOT NULL REFERENCES medicamentos(id),
    estudiante_id   INTEGER NOT NULL REFERENCES estudiantes(id),
    cantidad        INTEGER NOT NULL CHECK (cantidad > 0),
    estado          TEXT NOT NULL DEFAULT 'pendiente'
                    CHECK (estado IN ('pendiente','entregado','rechazado')),
    fecha           TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
);
