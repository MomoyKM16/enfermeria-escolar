// Datos de prueba (inventados). Luego se cambian por la base de datos.
const ESTUDIANTES = [
  { id: 1, nombre: "Ana Pérez",    grado: "5to", seccion: "A", edad: 11, tutor: "María Pérez",    telefono: "809-555-0101", alergias: "Ninguna",   condiciones: "Ninguna" },
  { id: 2, nombre: "Luis Gómez",   grado: "6to", seccion: "B", edad: 12, tutor: "Carlos Gómez",   telefono: "809-555-0102", alergias: "Penicilina", condiciones: "Asma" },
  { id: 3, nombre: "Sofía Reyes",  grado: "4to", seccion: "A", edad: 10, tutor: "Laura Reyes",    telefono: "809-555-0103", alergias: "Ninguna",   condiciones: "Ninguna" },
  { id: 4, nombre: "Diego Santos", grado: "6to", seccion: "A", edad: 12, tutor: "Rosa Santos",    telefono: "809-555-0104", alergias: "Polen",     condiciones: "Ninguna" },
  { id: 5, nombre: "Valeria Díaz", grado: "5to", seccion: "B", edad: 11, tutor: "Pedro Díaz",     telefono: "809-555-0105", alergias: "Ninguna",   condiciones: "Ninguna" }
];

// estado: "espera", "atendido" o "despachado"
// orientacion: true = la visita fue enviada a Orientación
const VISITAS = [
  { id: 1, estado: "atendido", estudianteId: 1, fecha: "2026-10-19", motivo: "Dolor de cabeza", tratamiento: "Reposo y agua",       orientacion: false, nota: "" },
  { id: 2, estado: "atendido", estudianteId: 2, fecha: "2026-10-20", motivo: "Crisis de asma leve", tratamiento: "Inhalador",       orientacion: false, nota: "" },
  { id: 3, estado: "atendido", estudianteId: 3, fecha: "2026-10-21", motivo: "Dolor de estómago frecuente", tratamiento: "Reposo", orientacion: true,  nota: "Dice sentirse nerviosa antes de los exámenes." },
  { id: 4, estado: "atendido", estudianteId: 4, fecha: "2026-10-22", motivo: "Golpe en la rodilla", tratamiento: "Hielo",           orientacion: false, nota: "" },
  { id: 5, estado: "espera", estudianteId: 3, fecha: "2026-10-22", motivo: "Mareo",         tratamiento: "Reposo",                orientacion: true,  nota: "Segunda visita esta semana." },
  { id: 6, estado: "despachado", estudianteId: 5, fecha: "2026-10-22", motivo: "Fiebre",        tratamiento: "Se llamó al tutor",     orientacion: false, nota: "" }
];