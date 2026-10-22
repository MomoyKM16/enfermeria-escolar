// Funciones que usan todas las pantallas.

// Saca el usuario guardado por el login.
// (Si tu login.js lo guarda con otro nombre, cambia "usuario" aquí.)
function obtenerUsuario() {
  try {
    return JSON.parse(sessionStorage.getItem("usuario"));
  } catch (e) {
    return null;
  }
}

// Si no hay sesión, manda al login.
function protegerPagina() {
  if (!obtenerUsuario()) {
    window.location.href = "login.html";
  }
}

function cerrarSesion() {
  sessionStorage.removeItem("usuario");
  window.location.href = "login.html";
}

// Muestra el nombre del usuario y el botón de salir en <div id="barra-usuario">
function mostrarBarra() {
  const barra = document.getElementById("barra-usuario");
  const usuario = obtenerUsuario();
  if (!barra || !usuario) return;
  barra.innerHTML =
    "<span>Hola, " + (usuario.nombre || usuario.usuario || "usuario") + "</span> " +
    '<button type="button" onclick="cerrarSesion()">Salir</button>';
}

// Lee un dato de la URL. Ejemplo: estudiante.html?id=2 -> obtenerParametro("id") = "2"
function obtenerParametro(nombre) {
  return new URLSearchParams(window.location.search).get(nombre);
}

function buscarEstudiante(id) {
  return ESTUDIANTES.find(function (e) { return e.id === Number(id); });
}

// Devuelve la etiqueta de color según el estado (usa las clases de base.css)
function etiquetaEstado(estado) {
  const textos = { espera: "En espera", atendido: "Atendido", despachado: "Despachado" };
  return '<span class="etiqueta estado-' + estado + '">' + textos[estado] + "</span>";
}