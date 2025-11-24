function initTemas() {
  const root = document.querySelector('#temas');
  if (!root) return;
  const mediaId = parseInt(root.getAttribute('data-media-id'));
  const apunteResumenIdAttr = root.getAttribute('data-apunte-resumen-id');
  const apunteResumenId = apunteResumenIdAttr ? parseInt(apunteResumenIdAttr) : null;
  const btnGenerar = document.getElementById('btnGenerarTemas');
  const btnRegenerar = document.getElementById('btnRegenerarTemas');
  const container = document.getElementById('temasContainer');

  async function cargarTemas() {
    try {
      const res = await fetch(`${root.dataset.temasListRoute}`.replace('MEDIA_ID', mediaId));
      const data = await res.json();
      const temas = data.temas || [];
      container.innerHTML = '';
      if (temas.length === 0) {
        container.innerHTML = '<div class="col-12"><div class="alert alert-info">No hay temas aún</div></div>';
        if (btnGenerar) btnGenerar.disabled = !data.apunteResumenId;
        return false;
      }
      temas.forEach(t => {
        const estructura = typeof t.estructura === 'string' ? JSON.parse(t.estructura) : (t.estructura||{});
        const sorted = (estructura.secciones||[]).sort((a,b)=> (a.orden||0)-(b.orden||0));
        const secciones = sorted.map(s => `<li class="list-group-item"><div class="d-flex align-items-center gap-2"><span class="badge bg-secondary text-capitalize">${s.tipoSeccion}</span><strong>${s.titulo}</strong></div><div class="mt-1 markdown-content">${s.contenido_html || s.contenido || ''}</div></li>`).join('');
        const col = document.createElement('div');
        col.className = 'col-12';
        const temaId = t.id_tema || t.id;
        col.innerHTML = `
          <div class="card h-100">
            <div class="card-body">
              <span class="badge bg-secondary">Tema</span>
              <h6 class="mt-2">${t.titulo_tema || t.tituloTema}</h6>
              <p class="text-muted">${t.descripcion||''}</p>
              ${secciones ? `<ul class="list-group list-group-flush">${secciones}</ul>` : ''}
            </div>
            <div class="card-footer">
              <div class="d-flex align-items-center gap-2 mb-2">
                <select class="form-select form-select-sm w-auto" data-expansion-select data-tema-id="${temaId}">
                  <option value="profundizar">Profundizar</option>
                  <option value="ejemplos">Ejemplos</option>
                  <option value="ejercicios">Ejercicios</option>
                </select>
                <button class="btn btn-sm btn-outline-primary" data-profundizar data-tema-id="${temaId}">
                  <i class="bi bi-arrow-up-right-circle me-1"></i>Profundizar
                </button>
              </div>
              <div class="border rounded p-2">
                <div class="row g-2 align-items-center">
                  <div class="col-auto">
                    <select class="form-select form-select-sm" data-seccion-tipo data-tema-id="${temaId}">
                      <option>introduccion</option>
                      <option>concepto</option>
                      <option>ejemplo</option>
                      <option>ejercicio</option>
                      <option>aplicacion</option>
                      <option>conclusion</option>
                      <option>referencia</option>
                    </select>
                  </div>
                  <div class="col">
                    <input type="text" class="form-control form-control-sm" placeholder="Título de sección" data-seccion-titulo data-tema-id="${temaId}">
                  </div>
                  <div class="col-12 mt-2">
                    <textarea class="form-control form-control-sm" rows="2" placeholder="Contenido" data-seccion-contenido data-tema-id="${temaId}"></textarea>
                  </div>
                  <div class="col-12 mt-2 d-flex justify-content-end">
                    <button class="btn btn-sm btn-success" data-agregar-seccion data-tema-id="${temaId}">
                      <i class="bi bi-plus-circle me-1"></i>Agregar sección
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>`;
        container.appendChild(col);
      });
      return true;
    } catch (e) {
      return false;
    }
  }

  async function generarTemas() {
    if (!apunteResumenId && btnGenerar) return;
    if (btnGenerar) {
      btnGenerar.disabled = true;
      btnGenerar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generando...';
    }
    try {
      const url = `${root.dataset.temasGenerarRoute}`.replace('MEDIA_ID', mediaId);
      const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' } });
      if (!res.ok) throw new Error('error');
      const intervalId = setInterval(async () => {
        const ok = await cargarTemas();
        if (ok) {
          clearInterval(intervalId);
          if (btnGenerar) {
            btnGenerar.disabled = false;
            btnGenerar.innerHTML = '<i class="bi bi-magic me-2"></i>Generar temas';
          }
        }
      }, 5000);
    } catch (e) {
      if (btnGenerar) {
        btnGenerar.disabled = false;
        btnGenerar.innerHTML = '<i class="bi bi-magic me-2"></i>Generar temas';
      }
    }
  }

  if (btnGenerar) btnGenerar.addEventListener('click', generarTemas);
  if (btnRegenerar) btnRegenerar.addEventListener('click', generarTemas);

  document.addEventListener('click', async (e) => {
    const profundizarBtn = e.target.closest('[data-profundizar]');
    const agregarBtn = e.target.closest('[data-agregar-seccion]');
    if (profundizarBtn) {
      const temaId = profundizarBtn.getAttribute('data-tema-id');
      const select = document.querySelector(`[data-expansion-select][data-tema-id="${temaId}"]`);
      const tipo = select ? select.value : 'profundizar';
      profundizarBtn.disabled = true;
      profundizarBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Procesando';
      try {
        const url = `${root.dataset.temasProfundizarRoute}`.replace('TEMA_ID', temaId);
        const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify({ tipoExpansion: tipo }) });
        if (!res.ok) throw new Error('error');
        await cargarTemas();
      } catch (err) {}
      profundizarBtn.disabled = false;
      profundizarBtn.innerHTML = '<i class="bi bi-arrow-up-right-circle me-1"></i>Profundizar';
    }
    if (agregarBtn) {
      const temaId = agregarBtn.getAttribute('data-tema-id');
      const tipoSel = document.querySelector(`[data-seccion-tipo][data-tema-id="${temaId}"]`);
      const tituloEl = document.querySelector(`[data-seccion-titulo][data-tema-id="${temaId}"]`);
      const contenidoEl = document.querySelector(`[data-seccion-contenido][data-tema-id="${temaId}"]`);
      const payload = { tipoSeccion: tipoSel.value, titulo: tituloEl.value, contenido: contenidoEl.value };
      agregarBtn.disabled = true;
      agregarBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Agregando';
      try {
        const url = `${root.dataset.temasSeccionesRoute}`.replace('TEMA_ID', temaId);
        const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json', 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
        if (!res.ok) throw new Error('error');
        tituloEl.value = '';
        contenidoEl.value = '';
        await cargarTemas();
      } catch (err) {}
      agregarBtn.disabled = false;
      agregarBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Agregar sección';
    }
  });

  window.addEventListener('pageshow', () => { cargarTemas(); });
}

document.addEventListener('DOMContentLoaded', initTemas);