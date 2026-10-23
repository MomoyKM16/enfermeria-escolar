const form = document.getElementById('form-login');
const mensaje = document.getElementById('mensaje');

form.addEventListener('submit', async (e) => {
  e.preventDefault(); // si no, la página se recarga sola
  mensaje.textContent = '';

  try {
    const usuario = await api('auth.php', {
      metodo: 'POST',
      params: { accion: 'login' },
      cuerpo: {
        usuario: document.getElementById('usuario').value.trim(),
        clave: document.getElementById('clave').value
      }
    });

    // NUEVO: guardar al usuario para que las otras pantallas sepan que hay sesión
    sessionStorage.setItem('usuario', JSON.stringify(usuario));

    if (usuario.rol === 'enfermera') {
      window.location.href = 'dashboard.html';
    } else {
      window.location.href = 'orientacion.html';
    }
  } catch (err) {
    mensaje.textContent = err.message;
  }
});