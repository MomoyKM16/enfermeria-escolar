protegerPagina();
mostrarBarra();

const estudiante = buscarEstudiante(obtenerParametro("id"));
const perfil = document.getElementById("perfil");

if (!estudiante) {
  perfil.innerHTML = "<h2>Estudiante no encontrado</h2><p class=\"suave\">Vuelve al inicio y elige uno de la lista.</p>";
  document.getElementById("datos").style.display = "none";
} else {
  // Iniciales para el círculo (Ana Pérez -> AP)
  const iniciales = estudiante.nombre.split(" ").map(function (p) { return p[0]; }).join("").slice(0, 2);

  perfil.innerHTML =
    '<div class="perfil">' +
    '<div class="avatar">' + iniciales + "</div>" +
    "<div><h2>" + estudiante.nombre + "</h2>" +
    "<p>" + estudiante.grado + " " + estudiante.seccion + ", " + estudiante.edad + " años</p></div>" +
    "</div>";

  // Avisos importantes para la enfermera
  let avisos = "";
  if (estudiante.alergias !== "Ninguna") {
    avisos += '<div class="alerta">Alergia: ' + estudiante.alergias + "</div>";
  }
  if (estudiante.condiciones !== "Ninguna") {
    avisos += '<div class="alerta">Condición: ' + estudiante.condiciones + "</div>";
  }
  document.getElementById("avisos").innerHTML = avisos;

  document.getElementById("datos").innerHTML =
    '<div><span class="campo">Tutor</span><strong>' + estudiante.tutor + "</strong></div>" +
    '<div><span class="campo">Teléfono</span><strong>' + estudiante.telefono + "</strong></div>" +
    '<div><span class="campo">Alergias</span><strong>' + estudiante.alergias + "</strong></div>" +
    '<div><span class="campo">Condiciones</span><strong>' + estudiante.condiciones + "</strong></div>";

  const tabla = document.getElementById("tabla-visitas");
  const visitas = VISITAS.filter(function (v) { return v.estudianteId === estudiante.id; });

  if (visitas.length === 0) {
    tabla.innerHTML = '<tr><td colspan="5">Sin visitas todavía.</td></tr>';
  }

  visitas.forEach(function (v) {
    tabla.innerHTML +=
      "<tr>" +
      "<td>" + v.fecha + "</td>" +
      "<td>" + v.motivo + "</td>" +
      "<td>" + v.tratamiento + "</td>" +
      "<td>" + etiquetaEstado(v.estado) + "</td>" +
      "<td>" + (v.orientacion ? "Sí" : "No") + "</td>" +
      "</tr>";
  });
}