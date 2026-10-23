# Contrato de la API

Todos usamos las mismas reglas. Si algo cambia, se avisa al grupo y se actualiza este archivo.

## Respuestas

Toda respuesta es JSON:

- Todo bien: `{"ok": true, "data": ...lo que se pidió...}`
- Algo mal: `{"ok": false, "error": "Mensaje en español"}`

## Valores fijos (escribirlos exactamente así)

- Roles: `enfermera` (Yajaira, puede todo) y `orientacion` (solo mirar).
- Estados de una visita: `espera` (amarillo), `atendido` (verde), `despachado` (rojo).
- Estados de una solicitud de medicamento: `pendiente`, `entregado`, `rechazado`.
- Regla de la alerta: un estudiante entra en alerta con 4 o más visitas `despachado` en los últimos 30 días.
  Viven en las constantes `ALERTA_MINIMA` y `DIAS_ALERTA` (`config/helpers.php`). Confirmar el periodo con el profesor.
- Usuarios de prueba: `yajaira / enfermera123` y `orientacion / orienta123` (cambiar antes de entregar).

## Lista de pedidos

| Pedido | Método | Quién puede | Qué hace | Dueño |
|---|---|---|---|---|
| `api/auth.php?accion=login` | POST | todos | Entra con `{usuario, clave}` | P1 |
| `api/auth.php?accion=sesion` | GET | logueado | Dice quién soy y mi rol | P1 |
| `api/auth.php?accion=logout` | POST | logueado | Cierra sesión | P1 |
| `api/estudiantes.php?q=ana&curso=3A` | GET | ambos | Lista y busca estudiantes | P2 |
| `api/estudiantes.php?id=5` | GET | ambos | Un estudiante con su historial | P2 |
| `api/estudiantes.php` | POST | enfermera | Crea estudiante | P2 |
| `api/estudiantes.php?id=5` | PUT | enfermera | Edita estudiante | P2 |
| `api/visitas.php?estudiante_id=5` | GET | ambos | Historial de visitas | P2 |
| `api/visitas.php` | POST | enfermera | Registra una visita | P2 |
| `api/visitas.php?id=9` | PUT | enfermera | Cambia estado o notas | P2 |
| `api/alertas.php` | GET | ambos | Estudiantes en alerta + teléfono de los padres | P2 |
| `api/medicamentos.php` | GET | ambos | Medicamentos y stock | P2 |
| `api/solicitudes.php` | GET / POST / PUT | GET ambos, resto enfermera | Pedidos de medicamento | P2 |

Ejemplo de lo que devuelve `GET api/estudiantes.php`:

```json
{"ok": true, "data": [
  {"id": 5, "nombre": "Ana", "apellido": "Pérez", "curso": "3A",
   "estado_actual": "espera", "despachos_recientes": 2}
]}
```

`estado_actual` es el estado de la última visita del estudiante (o `null` si nunca ha ido).
`despachos_recientes` cuenta los despachos de los últimos 30 días.

**Por confirmar con el profesor:** ¿"pedir medicamento" es pedir un medicamento para un estudiante, o pedir reposición del stock?
Este plan asume lo primero (cada solicitud tiene medicamento, estudiante y cantidad).

## Quién es dueño de qué archivo

Si necesitas cambiar un archivo que no es tuyo, pídeselo a su dueño.

| Persona | Archivos propios |
|---|---|
| P1 | `config/`, `database/`, `api/auth.php`, `public/js/api.js`, `docs/` |
| P2 | `api/estudiantes.php`, `visitas.php`, `alertas.php`, `medicamentos.php`, `solicitudes.php` |
| P3 | `public/login.html`, `dashboard.html`, `estudiante.html`, `orientacion.html`, `css/base.css` y sus `.js` |
| P4 | `public/consulta.html`, `medicamentos.html`, `js/ayuda.js`, `css/formularios.css` y sus `.js` |

## Ayudante de JavaScript: `api()`

Está en `public/js/api.js`. Devuelve `data` si todo va bien y lanza un error con el mensaje si no.
Si el servidor responde 401 (sesión vencida), manda al login.

**Ejemplo 1: con parámetros (GET)**

```js
const lista = await api('estudiantes.php', { params: { q: 'ana' } });
```

**Ejemplo 2: con método POST y cuerpo**

```js
const nuevo = await api('visitas.php', {
  metodo: 'POST',
  cuerpo: { estudiante_id: 5, motivo: 'Dolor de cabeza', estado: 'espera' }
});
```

## Ayudantes de PHP (`config/`)

Al inicio de cada archivo de `api/`:

```php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../config/helpers.php';
requerir_login();            // si lo pueden ver los dos roles
// requerir_rol('enfermera'); // si solo Yajaira puede
```

| Función | Qué hace |
|---|---|
| `db()` | Conexión PDO a SQLite |
| `responder($data, $codigo = 200)` | Imprime `{ok:true, data:...}` y termina |
| `responder_error($mensaje, $codigo = 400)` | Imprime `{ok:false, error:...}` y termina |
| `entrada()` | Lee el JSON que mandó el navegador como arreglo |
| `requerir_login()` | Sin sesión: error 401 |
| `requerir_rol($rol)` | Rol distinto: error 403 |
