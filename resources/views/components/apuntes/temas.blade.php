<div 
  class="tab-pane fade" 
  id="temas" 
  role="tabpanel" 
  aria-labelledby="temas-tab" 
  data-media-id="{{ (int)$mediaId }}" 
  data-apunte-resumen-id="{{ $apunteResumenId ? (int)$apunteResumenId : '' }}" 
  data-temas-list-route="{{ route('client.apuntes.temas', ['id' => 'MEDIA_ID']) }}" 
  data-temas-generar-route="{{ route('client.apuntes.temas.generar', ['id' => 'MEDIA_ID']) }}" 
  data-temas-profundizar-route="{{ route('client.apuntes.temas.profundizar', ['temaId' => 'TEMA_ID']) }}" 
  data-temas-secciones-route="{{ route('client.apuntes.temas.secciones', ['temaId' => 'TEMA_ID']) }}"
>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Temas estructurados</h5>
      <div>
        @if(empty($temas) || count($temas)===0)
          <button class="btn btn-primary" id="btnGenerarTemas" @if(!$apunteResumenId) disabled @endif>
            <i class="bi bi-magic me-2"></i>Generar temas
          </button>
        @else
          <button class="btn btn-outline-primary" id="btnRegenerarTemas">
            <i class="bi bi-arrow-repeat me-2"></i>Regenerar
          </button>
        @endif
      </div>
    </div>
  
    <div id="temasContainer" class="row g-3">
      @forelse($temas as $t)
        <div class="col-12">
          <div class="card h-100">
            <div class="card-body">
              <span class="badge bg-secondary">Tema</span>
              <h6 class="mt-2">{{ $t->titulo_tema ?? $t->tituloTema }}</h6>
              <p class="text-muted">{{ $t->descripcion }}</p>
              @php $estructura = is_string($t->estructura) ? json_decode($t->estructura, true) : $t->estructura; @endphp
              @if(!empty($estructura['secciones']))
                @php $secs = collect($estructura['secciones'])->sortBy('orden')->all(); @endphp
                <ul class="list-group list-group-flush">
                  @foreach($secs as $s)
                    <li class="list-group-item">
                      <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary text-capitalize">{{ $s['tipoSeccion'] }}</span>
                        <strong>{{ $s['titulo'] }}</strong>
                      </div>
                      @php
                        $htmlContenido = isset($s['contenido_html'])
                          ? $s['contenido_html']
                          : \Illuminate\Support\Str::markdown($s['contenido'] ?? '');
                      @endphp
                      <div class="mt-1 markdown-content">{!! $htmlContenido !!}</div>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
            <div class="card-footer">
              <div class="d-flex align-items-center gap-2 mb-2">
                <select class="form-select form-select-sm w-auto" data-expansion-select data-tema-id="{{ $t->id_tema ?? $t->id }}">
                  <option value="profundizar">Profundizar</option>
                  <option value="ejemplos">Ejemplos</option>
                  <option value="ejercicios">Ejercicios</option>
                </select>
                <button class="btn btn-sm btn-outline-primary" data-profundizar data-tema-id="{{ $t->id_tema ?? $t->id }}">
                  <i class="bi bi-arrow-up-right-circle me-1"></i>Profundizar
                </button>
              </div>
              <div class="border rounded p-2">
                <div class="row g-2 align-items-center">
                  <div class="col-auto">
                    <select class="form-select form-select-sm" data-seccion-tipo data-tema-id="{{ $t->id_tema ?? $t->id }}">
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
                    <input type="text" class="form-control form-control-sm" placeholder="Título de sección" data-seccion-titulo data-tema-id="{{ $t->id_tema ?? $t->id }}">
                  </div>
                  <div class="col-12 mt-2">
                    <textarea class="form-control form-control-sm" rows="2" placeholder="Contenido" data-seccion-contenido data-tema-id="{{ $t->id_tema ?? $t->id }}"></textarea>
                  </div>
                  <div class="col-12 mt-2 d-flex justify-content-end">
                    <button class="btn btn-sm btn-success" data-agregar-seccion data-tema-id="{{ $t->id_tema ?? $t->id }}">
                      <i class="bi bi-plus-circle me-1"></i>Agregar sección
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-info">No hay temas aún</div>
        </div>
      @endforelse
    </div>
  
    
  </div>