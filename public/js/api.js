// Ayudante para hablar con la API desde cualquier pantalla.
// Uso:
//   const lista = await api('estudiantes.php', { params: { q: 'ana' } });
//   await api('visitas.php', { metodo: 'POST', cuerpo: { estudiante_id: 5, motivo: 'Fiebre' } });
//   const yo = await api('auth.php', { params: { accion: 'sesion' } });
async function api(archivo, { metodo = 'GET', params = {}, cuerpo = null } = {}) {
    // 1. Armar la direccion: ../api/archivo?clave=valor
    let url = '../api/' + archivo;
    const query = new URLSearchParams(params).toString();
    if (query) url += (url.includes('?') ? '&' : '?') + query;

    // 2. Hacer el pedido (credentials: same-origin hace que viaje la sesion)
    const res = await fetch(url, {
        method: metodo,
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json' },
        body: cuerpo ? JSON.stringify(cuerpo) : null,
    });

    // 3. Sesion vencida -> al login (menos si estamos en auth.php, para no ciclar)
    if (res.status === 401 && !archivo.startsWith('auth.php')) {
        window.location.href = 'login.html';
        throw new Error('Sesión vencida');
    }

    let json;
    try {
        json = await res.json();
    } catch (e) {
        throw new Error('El servidor respondió algo inesperado');
    }

    // 4. Error -> excepcion con el mensaje; bien -> devolver los datos
    if (!json.ok) throw new Error(json.error);
    return json.data;
}
