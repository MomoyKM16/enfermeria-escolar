-- Datos de prueba. TODO es inventado (telefonos 809-555-01xx).
-- Los usuarios (yajaira, orientacion) los crea instalar.php.

INSERT INTO estudiantes (matricula, nombre, apellido, curso, nombre_padre, telefono_padre) VALUES
('2026-001','Ana','Pérez','3A','Familia Pérez','809-555-0101'),
('2026-002','Juan','Gómez','3A','Familia Gómez','809-555-0102'),
('2026-003','María','Rodríguez','3B','Familia Rodríguez','809-555-0103'),
('2026-004','Carlos','Martínez','4A','Familia Martínez','809-555-0104'),
('2026-005','Luisa','Fernández','1A','Familia Fernández','809-555-0105'),
('2026-006','Pedro','Santana','1A','Familia Santana','809-555-0106'),
('2026-007','Rosa','Díaz','1B','Familia Díaz','809-555-0107'),
('2026-008','Miguel','Castillo','2A','Familia Castillo','809-555-0108'),
('2026-009','Laura','Jiménez','2A','Familia Jiménez','809-555-0109'),
('2026-010','José','Reyes','2B','Familia Reyes','809-555-0110'),
('2026-011','Carmen','Núñez','2B','Familia Núñez','809-555-0111'),
('2026-012','Luis','Peña','3A','Familia Peña','809-555-0112'),
('2026-013','Sofía','Almonte','3B','Familia Almonte','809-555-0113'),
('2026-014','Diego','Ramírez','4A','Familia Ramírez','809-555-0114'),
('2026-015','Valeria','Mejía','4B','Familia Mejía','809-555-0115'),
('2026-016','Andrés','Cabrera','4B','Familia Cabrera','809-555-0116'),
('2026-017','Camila','Féliz','5A','Familia Féliz','809-555-0117'),
('2026-018','Daniel','Sosa','5A','Familia Sosa','809-555-0118'),
('2026-019','Isabel','Taveras','5B','Familia Taveras','809-555-0119'),
('2026-020','Rafael','Polanco','5B','Familia Polanco','809-555-0120'),
('2026-021','Gabriela','Medina','6A','Familia Medina','809-555-0121'),
('2026-022','Samuel','Duarte','6A','Familia Duarte','809-555-0122'),
('2026-023','Natalia','Vargas','6B','Familia Vargas','809-555-0123'),
('2026-024','Emmanuel','Rosario','1B','Familia Rosario','809-555-0124'),
('2026-025','Patricia','Guzmán','1A','Familia Guzmán','809-555-0125'),
('2026-026','Héctor','Báez','2A','Familia Báez','809-555-0126'),
('2026-027','Yuleisy','Ortiz','3B','Familia Ortiz','809-555-0127'),
('2026-028','Kevin','Mora','4A','Familia Mora','809-555-0128'),
('2026-029','Altagracia','Cruz','5A','Familia Cruz','809-555-0129'),
('2026-030','Ramón','Pichardo','6B','Familia Pichardo','809-555-0130');

INSERT INTO medicamentos (nombre, stock) VALUES
('Acetaminofén', 50),
('Ibuprofeno', 30),
('Sales de rehidratación', 20);

-- CASO 1: Juan (id 2) tiene 4 despachos en los ultimos 30 dias -> DEBE salir en alerta
INSERT INTO visitas (estudiante_id, fecha, motivo, estado) VALUES
(2, datetime('now','-1 days'),  'Dolor de cabeza', 'despachado'),
(2, datetime('now','-4 days'),  'Dolor de estómago', 'despachado'),
(2, datetime('now','-9 days'),  'Mareo', 'despachado'),
(2, datetime('now','-15 days'), 'Dolor de cabeza', 'despachado');

-- CASO 2: María (id 3) tiene solo 3 despachos -> NO debe salir
INSERT INTO visitas (estudiante_id, fecha, motivo, estado) VALUES
(3, datetime('now','-2 days'),  'Fiebre', 'despachado'),
(3, datetime('now','-6 days'),  'Dolor de garganta', 'despachado'),
(3, datetime('now','-11 days'), 'Dolor de cabeza', 'despachado');

-- CASO 3: Carlos (id 4) tiene 4 despachos pero VIEJOS (mas de 30 dias) -> NO debe salir
INSERT INTO visitas (estudiante_id, fecha, motivo, estado) VALUES
(4, datetime('now','-35 days'), 'Dolor de cabeza', 'despachado'),
(4, datetime('now','-40 days'), 'Mareo', 'despachado'),
(4, datetime('now','-47 days'), 'Dolor de estómago', 'despachado'),
(4, datetime('now','-55 days'), 'Fiebre', 'despachado');

-- Ana (id 1): 2 despachos recientes y su ultima visita esta en espera
INSERT INTO visitas (estudiante_id, fecha, motivo, estado) VALUES
(1, datetime('now','-20 days'), 'Dolor de cabeza', 'despachado'),
(1, datetime('now','-12 days'), 'Dolor de oído', 'despachado'),
(1, datetime('now'),            'Dolor de estómago', 'espera');

-- Visitas en espera y atendido para los colores del dashboard
INSERT INTO visitas (estudiante_id, fecha, motivo, estado) VALUES
(5, datetime('now','-1 days'), 'Golpe en la rodilla', 'atendido'),
(6, datetime('now'),           'Náuseas', 'atendido'),
(7, datetime('now'),           'Dolor de cabeza', 'espera'),
(8, datetime('now','-3 days'), 'Fiebre', 'despachado'),
(9, datetime('now','-5 days'), 'Mareo', 'atendido');
-- (los demas estudiantes no tienen visitas)

-- Una solicitud de medicamento pendiente para probar
INSERT INTO solicitudes_medicamento (medicamento_id, estudiante_id, cantidad) VALUES (1, 2, 2);
