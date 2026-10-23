# Sistema de Enfermería Escolar

Sistema para que la enfermera (Yajaira) registre consultas de estudiantes, vea el historial, reciba alertas
cuando un estudiante es despachado muchas veces, y pida medicamentos. Orientación puede ver todo, pero no modificar nada.

Hecho con PHP, SQLite, HTML, CSS y JavaScript puro.

## Cómo levantarlo (4 pasos)

Requisitos: PHP 8 con `pdo_sqlite` (comprobar con `php -m`), y Git.

```
git clone https://github.com/MomoyKM16/enfermeria-escolar.git
cd enfermeria-escolar
php database/instalar.php
php -S localhost:8000
```

Luego abrir en el navegador: <http://localhost:8000/public/login.html>

(Para apagar el servidor: `Ctrl + C`.)

## Usuarios de prueba

| Usuario | Clave | Rol |
|---|---|---|
| yajaira | enfermera123 | enfermera (puede todo) |
| orientacion | orienta123 | orientacion (solo mirar) |

Todos los datos de estudiantes son inventados.

## Integrantes

| Persona | Nombre | Parte |
|---|---|---|
| 1 | Moisés | Base de datos, config, login (`api/auth.php`), `api.js`, documentación |
| 2 | (nombre) | API de datos: estudiantes, visitas, alertas, medicamentos, solicitudes |
| 3 | (nombre) | Pantallas principales: login, dashboard, perfil, vista de Orientación |
| 4 | (nombre) | Formularios, medicamentos, ayuda, pruebas y manual |

## Documentación

El contrato de la API está en [`docs/API.md`](docs/API.md).
