protegerPagina();
mostrarBarra();

// Solo las visitas marcadas para Orientación
const casos = VISITAS.filter(function (v) { return v.orientacion; });
const tabla = document.getElementById("tabla-casos");

if (casos.length === 0) {
  document.getElementById("mensaje").textContent = "No hay casos por ahora.";
}

casos.forEach(function (v) {
  const est = buscarEstudiante(v.estudianteId);
  tabla.innerHTML +=
    "<tr>" +
    "<td>" + v.fecha + "</td>" +
    "<td>" + est.nombre + " (" + est.grado + " " + est.seccion + ")</td>" +
    "<td>" + v.motivo + "</td>" +
    "<td>" + etiquetaEstado(v.estado) + "</td>" +
    "<td>" + v.nota + "</td>" +
    '<td><a href="estudiante.html?id=' + est.id + '">Ver perfil</a></td>' +
    "</tr>";
});