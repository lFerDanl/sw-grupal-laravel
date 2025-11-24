@extends('Layouts.client')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">Mis contenidos</h2>
      <p class="text-muted mb-0">Gestiona tus clases y material de estudio</p>
    </div>
    <a class="btn btn-primary btn-lg" href="{{ route('client.apuntes.ia.media') }}">
      <i class="bi bi-plus-circle me-2"></i>Añadir contenido
    </a>
  </div>

  @if(session('upload_ok'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ session('message') ?? 'Media subida y encolada' }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row g-4" id="mediasContainer">
    @forelse($medias as $m)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm hover-lift">
          <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-start mb-3">
              <div class="flex-grow-1">
                <h5 class="card-title mb-2">{{ $m['titulo'] }}</h5>
                <p class="card-text text-muted small">{{ Str::limit($m['descripcion'], 100) }}</p>
              </div>
              @if($m['tipo'] === 'VIDEO')
                <div class="ms-2">
                  <span class="badge bg-primary-subtle text-primary rounded-pill">
                    <i class="bi bi-camera-video"></i>
                  </span>
                </div>
              @else
                <div class="ms-2">
                  <span class="badge bg-info-subtle text-info rounded-pill">
                    <i class="bi bi-music-note"></i>
                  </span>
                </div>
              @endif
            </div>

            <div class="mb-3">
              <div class="d-flex flex-wrap gap-2">
                <span class="badge {{ $m['transcripcion_status']==='completado' ? 'bg-success' : ($m['transcripcion_status']==='procesando' ? 'bg-warning text-dark' : ($m['transcripcion_status']==='error' ? 'bg-danger':'bg-secondary')) }}">
                  @if($m['transcripcion_status']==='completado')
                    <i class="bi bi-check-circle me-1"></i>
                  @elseif($m['transcripcion_status']==='procesando')
                    <i class="bi bi-hourglass-split me-1"></i>
                  @elseif($m['transcripcion_status']==='error')
                    <i class="bi bi-exclamation-triangle me-1"></i>
                  @else
                    <i class="bi bi-circle me-1"></i>
                  @endif
                  Transcripción: {{ $m['transcripcion_status'] }}
                </span>
              </div>
            </div>

            <div class="mt-auto">
              @if($m['apuntes_ready'])
                <a href="{{ route('client.apuntes.show', ['id' => $m['id_media']]) }}" class="btn btn-success w-100">
                  <i class="bi bi-eye me-2"></i>Ver apuntes
                </a>
              @else
                <button class="btn btn-outline-secondary w-100" disabled>
                  <span class="spinner-border spinner-border-sm me-2"></span>
                  Generando apuntes...
                </button>
              @endif
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12" id="emptyState">
        <div class="text-center py-5">
          <div class="mb-4">
            <i class="bi bi-folder-x" style="font-size: 4rem; color: var(--bs-secondary);"></i>
          </div>
          <h4 class="text-muted mb-3">Aún no tienes contenidos</h4>
          <p class="text-muted mb-4">¡Sube tu primera clase y comienza a generar apuntes automáticamente!</p>
          <a class="btn btn-primary btn-lg" href="{{ route('client.apuntes.ia.media') }}">
            <i class="bi bi-plus-circle me-2"></i>Subir primera clase
          </a>
        </div>
      </div>
    @endforelse
  </div>

</div>

<style>
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
</style>
<!-- Se eliminó el modal de subida; ahora se usa una página dedicada -->
@endsection