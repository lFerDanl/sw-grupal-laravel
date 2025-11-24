@extends('Layouts.client')

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1"><i class="bi bi-cloud-upload me-2"></i>Subir media</h2>
      <p class="text-muted mb-0">Carga tu clase en video o audio para generar apuntes automáticamente</p>
    </div>
    <a href="{{ route('client.apuntes.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left me-2"></i>Volver a contenidos
    </a>
  </div>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row">
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body">
          <form id="uploadForm" action="{{ route('client.ia.media.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
              <label class="form-label fw-semibold">Archivo (video/audio)</label>
              <input type="file" name="file" class="form-control" accept="video/*,audio/*" required>
              <div class="form-text">Formatos soportados: MP4, MP3, WAV, etc.</div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Título</label>
              <input type="text" name="titulo" class="form-control" maxlength="200" placeholder="Ej: Clase 1 - Introducción a Laravel" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3" placeholder="Describe brevemente el contenido de la clase..."></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Tipo</label>
              <select name="tipo" class="form-select" required>
                <option value="VIDEO">VIDEO</option>
                <option value="AUDIO">AUDIO</option>
              </select>
            </div>

            <div class="d-flex justify-content-end gap-2">
              <a href="{{ route('client.apuntes.index') }}" class="btn btn-secondary">Cancelar</a>
              <button class="btn btn-primary" type="submit" id="btnSubmit">
                <i class="bi bi-upload me-2"></i>Subir y procesar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection