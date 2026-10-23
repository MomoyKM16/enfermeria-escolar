protegerPagina();
mostrarBarra();

// 1. Alerta si hay estudiantes en espera
function mostrarAlertas() {
  const enEspera = VISITAS.filter(function (v) { return v.estado === "espera"; }).length;
  const caja = document.getElementById("alertas");
  caja.innerHTML = enEspera > 0
    ? '<div class="alerta">Hay ' + enEspera + ' estudiante(s) en espera.</div>'
    : "";
}

// 2. Resumen (3 números)
function mostrarResumen() {
  const hoy = "2026-10-22";
  const visitasHoy = VISITAS.filter(function (v) { return v.fecha === hoy; }).length;
  const enOrientacion = VISITAS.filter(function (v) { return v.orientacion; }).length;

  document.getElementById("resumen").innerHTML =
    '<div class="dato"><strong>' + ESTUDIANTES.length + "</strong><span>Estudiantes</span></div>" +
    '<div class="dato"><strong>' + visitasHoy + "</strong><span>Visitas hoy</span></div>" +
    '<div class="dato"><strong>' + enOrientacion + "</strong><span>En Orientación</span></div>";
}

// 3. Tabla de estudiantes (con buscador)
function mostrarEstudiantes(texto) {
  const filtro = (texto || "").toLowerCase();
  const tabla = document.getElementById("tabla-estudiantes");
  tabla.innerHTML = "";

  ESTUDIANTES.forEach(function (e) {
    if (e.nombre.toLowerCase().indexOf(filtro) === -1) return;
    tabla.innerHTML +=
      "<tr>" +
      "<td>" + e.nombre + "</td>" +
      "<td>" + e.grado + "</td>" +
      "<td>" + e.seccion + "</td>" +
      '<td><a href="estudiante.html?id=' + e.id + '">Ver perfil</a></td>' +
      "</tr>";
  });

  if (tabla.innerHTML === "") {
    tabla.innerHTML = '<tr><td colspan="4">No se encontró ningún estudiante.</td></tr>';
  }
}

// 4. Últimas 5 visitas
function mostrarUltimasVisitas() {
  const lista = document.getElementById("ultimas-visitas");
  const ultimas = VISITAS.slice().reverse().slice(0, 5);
  lista.innerHTML = "";

  ultimas.forEach(function (v) {
    const est = buscarEstudiante(v.estudianteId);
    lista.innerHTML +=
      "<li>" +
      "<div><strong>" + est.nombre + "</strong>" +
      "<small>" + v.motivo + "</small>" +
      "<small>" + v.fecha + "</small></div>" +
      etiquetaEstado(v.estado) +
      "</li>";
  });
}

document.getElementById("buscador").addEventListener("input", function (e) {
  mostrarEstudiantes(e.target.value);
});

mostrarAlertas();
mostrarResumen();
mostrarEstudiantes("");
mostrarUltimasVisitas();