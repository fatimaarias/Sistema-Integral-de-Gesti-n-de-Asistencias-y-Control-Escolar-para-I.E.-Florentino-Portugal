@extends('layouts.app')

@section('content')
@php
  // Parámetros de vista
  $size = (int) request('size', 160);
  $size = max(120, min(260, $size));
  $grado_id   = $grado_id   ?? request('grado_id');
  $seccion_id = $seccion_id ?? request('seccion_id');

  $listaAlumnos      = $alumnos ?? collect();
  $totalCredenciales = $listaAlumnos->count();

  $gradoSel   = ($grados ?? collect())->firstWhere('id', $grado_id);
  $seccionSel = ($secciones ?? collect())->firstWhere('id', $seccion_id);
@endphp

<style>
  .qr-bg{
    background: radial-gradient(circle at top left,#e0f2fe 0,#eef2ff 40%,#f9fafb 100%);
    min-height: calc(100vh - 80px);
    border-radius: 32px;
    padding: 24px;
  }

  .qr-header-title{
    font-weight:700;
    font-size:1.6rem;
    color:#0f172a;
  }
  .qr-header-sub{
    font-size:.95rem;
    color:#6b7280;
  }

  .chip-soft{
    display:inline-flex;
    align-items:center;
    gap:6px;
    border-radius:999px;
    padding:6px 12px;
    font-size:.8rem;
    font-weight:600;
    background:#eef2ff;
    color:#4f46e5;
  }
  .chip-count{
    background:#ecfeff;
    color:#0e7490;
  }

  .qr-toolbar-card{
    border-radius:22px;
    border:none;
    background:#ffffff;
    box-shadow:0 18px 40px rgba(148,163,184,.35);
    padding:16px 18px;
    margin-bottom:1rem;
  }

  .qr-toolbar{
    display:flex;
    gap:.75rem;
    align-items:center;
    flex-wrap:wrap;
  }

  .qr-select,
  .qr-input{
    border:1px solid #d1d5db;
    border-radius:999px;
    padding:.45rem .85rem;
    font-size:.9rem;
    min-width:160px;
  }

  .qr-search-wrap{
    position:relative;
    flex:1 1 220px;
  }
  .qr-search-icon{
    position:absolute;
    left:11px;
    top:50%;
    transform:translateY(-50%);
    font-size:.9rem;
    color:#9ca3af;
  }
  .qr-input.search{
    padding-left:30px;
    width:100%;
  }

  .qr-btn{
    border-radius:999px;
    padding:.48rem 1.1rem;
    font-size:.86rem;
    font-weight:600;
    border:1px solid transparent;
    display:inline-flex;
    align-items:center;
    gap:6px;
    cursor:pointer;
    text-decoration:none;
    white-space:nowrap;
  }
  .qr-btn-primary{
    background:linear-gradient(120deg,#6366f1,#a855f7);
    color:#f9fafb;
    box-shadow:0 14px 32px rgba(79,70,229,.55);
  }
  .qr-btn-outline{
    background:#ffffff;
    color:#111827;
    border-color:#d1d5db;
  }
  .qr-btn-dark{
    background:#111827;
    color:#f9fafb;
    box-shadow:0 12px 30px rgba(15,23,42,.65);
  }
  .qr-btn-sm{
    padding:.35rem .85rem;
    font-size:.8rem;
  }
  .qr-btn:hover{
    filter:brightness(1.05);
    text-decoration:none;
  }

  .qr-alert-success{
    background:#dcfce7;
    color:#065f46;
    border:1px solid #a7f3d0;
    border-radius:999px;
    padding:.55rem 1rem;
    font-size:.85rem;
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-bottom:1rem;
  }

  .qr-grid{
    display:grid;
    gap:18px;
    grid-template-columns:repeat(1,minmax(0,1fr));
  }
  @media (min-width:576px){ .qr-grid{ grid-template-columns:repeat(2,1fr) } }
  @media (min-width:992px){ .qr-grid{ grid-template-columns:repeat(3,1fr) } }
  @media (min-width:1200px){ .qr-grid{ grid-template-columns:repeat(4,1fr) } }

  .qr-card{
    position:relative;
    border-radius:22px;
    background:linear-gradient(145deg,#ffffff,#eff6ff);
    box-shadow:0 18px 40px rgba(148,163,184,.35);
    padding:14px 14px 16px;
    text-align:center;
    page-break-inside:avoid;
    overflow:hidden;
  }
  .qr-card::before{
    content:'';
    position:absolute;
    inset:0;
    border-radius:inherit;
    border:1px solid rgba(148,163,184,.35);
    pointer-events:none;
  }
  .qr-card-accent{
    position:absolute;
    inset:auto -20px 0;
    height:4px;
    background:linear-gradient(90deg,#4f46e5,#a855f7);
    opacity:.4;
  }

  .qr-name{
    font-weight:700;
    font-size:.95rem;
    color:#111827;
    margin-bottom:.1rem;
  }
  .qr-grade{
    font-size:.78rem;
    color:#6b7280;
  }

  .qr-status-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    margin-top:.45rem;
    font-size:.75rem;
    font-weight:600;
    border-radius:999px;
    padding:.15rem .7rem;
    background:#22c55e1a;
    color:#15803d;
  }
  .qr-status-badge.inactive{
    background:#fee2e2;
    color:#b91c1c;
  }

  .qr-img{
    margin:10px auto 8px;
    background:#ffffff;
    padding:6px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(15,23,42,.25);
    display:inline-block;
  }
  .qr-img svg{
    display:block;
  }

  .badge-warn{
    display:inline-block;
    background:#f97316;
    color:#fff;
    border-radius:999px;
    padding:.18rem .7rem;
    font-size:.75rem;
  }

  .qr-card-actions{
    display:flex;
    justify-content:center;
    gap:.4rem;
    margin-top:.1rem;
  }

  .muted{
    color:#6b7280;
    font-size:.9rem;
  }

  .qr-extra-actions{
    display:flex;
    justify-content:flex-end;
    margin-top:.35rem;
  }

  @media (max-width:992px){
    .qr-bg{
      border-radius:0;
      padding:16px;
    }
    .qr-extra-actions{
      justify-content:flex-start;
    }
  }

  @media print{
    header,nav,.menu-toggle,
    .qr-header,.qr-toolbar-card,
    .qr-extra-actions{display:none!important}
    body{background:#ffffff;}
    .qr-bg{background:#ffffff;border-radius:0;padding:0;}
    .qr-grid{grid-template-columns:repeat(3,1fr)!important;gap:10px;}
    .qr-card{box-shadow:none;background:#ffffff;}
    .qr-card::before{border:1px solid #e5e7eb;}
  }
</style>

<div class="qr-bg">
  <div class="container-xxl">

    {{-- Encabezado --}}
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center mb-3 qr-header">
      <div>
        <h1 class="qr-header-title mb-1">
          <i class="bi bi-qr-code-scan me-2"></i>
          Credenciales con código QR
        </h1>
        <p class="qr-header-sub mb-0">
          Cada QR abre una URL en el celular y registra la asistencia automáticamente.<br>
          Regla: <strong>antes de 13:00 &rarr; ASISTIÓ</strong>, <strong>desde 13:00 &rarr; TARDE</strong>.
        </p>
      </div>

      <div class="d-flex flex-column align-items-lg-end align-items-start mt-3 mt-lg-0 gap-2">
        <span class="chip-soft chip-count">
          <i class="bi bi-people-fill"></i>
          <span>Mostrando {{ $totalCredenciales }} alumno(s)</span>
        </span>
        <small class="text-muted" style="font-size:.78rem;">
          Filtro actual:
          {{ $gradoSel ? ($gradoSel->nombre . '°') : 'Todos los grados' }}
          &nbsp;•&nbsp;
          {{ $seccionSel ? $seccionSel->nombre : 'Todas las secciones' }}
        </small>
      </div>
    </div>

    {{-- Mensaje de éxito --}}
    @if (session('success'))
      <div class="qr-alert-success">
        <i class="bi bi-check-circle-fill"></i>
        <span>{{ session('success') }}</span>
      </div>
    @endif

    {{-- Toolbar de filtros --}}
    <div class="qr-toolbar-card">
      <form method="GET" class="qr-toolbar">
        <select name="grado_id" class="qr-select">
          <option value="">Todos los grados</option>
          @foreach(($grados ?? []) as $g)
            <option value="{{ $g->id }}" {{ (string)$grado_id === (string)$g->id ? 'selected' : '' }}>
              {{ $g->nombre }}
            </option>
          @endforeach
        </select>

        <select name="seccion_id" class="qr-select">
          <option value="">Todas las secciones</option>
          @foreach(($secciones ?? []) as $s)
            <option value="{{ $s->id }}" {{ (string)$seccion_id === (string)$s->id ? 'selected' : '' }}>
              Sección {{ $s->nombre }}
            </option>
          @endforeach
        </select>

        <select name="size" class="qr-select" title="Tamaño del QR">
          @foreach([120,140,160,180,200,220,240,260] as $opt)
            <option value="{{ $opt }}" {{ $size===$opt?'selected':'' }}>{{ $opt }} px</option>
          @endforeach
        </select>

        <div class="qr-search-wrap">
          <i class="bi bi-search qr-search-icon"></i>
          <input type="text"
                 id="q"
                 class="qr-input search"
                 placeholder="Buscar por nombre, grado o sección..."
                 oninput="filtrar()">
        </div>

        <button class="qr-btn qr-btn-primary" type="submit">
          <i class="bi bi-funnel"></i>
          <span>Aplicar filtros</span>
        </button>

        <a href="{{ route('alumnos.credenciales') }}" class="qr-btn qr-btn-outline">
          <i class="bi bi-x-circle"></i>
          <span>Limpiar</span>
        </a>

        <button type="button" class="qr-btn qr-btn-dark" onclick="window.print()">
          <i class="bi bi-printer-fill"></i>
          <span>Imprimir</span>
        </button>
      </form>
    </div>

    {{-- Botón global para generar QRs faltantes --}}
    <div class="qr-extra-actions">
      <form method="POST" action="{{ route('alumnos.qr.generar') }}">
        @csrf
        <button type="submit"
                class="qr-btn qr-btn-primary qr-btn-sm"
                onclick="return confirm('¿Crear códigos QR para todos los alumnos que aún no tienen token?');">
          <i class="bi bi-gear-fill"></i>
          <span>Generar QRs faltantes</span>
        </button>
      </form>
    </div>

    {{-- Grid de credenciales --}}
    @if($listaAlumnos->isEmpty())
      <p class="muted mt-3">
        No hay alumnos para mostrar con los filtros actuales.
        Prueba cambiando el grado o la sección, o limpia los filtros.
      </p>
    @else
      <div id="grid" class="qr-grid mt-3">
        @foreach($alumnos as $a)
          @php
            $nombre = $a->nombre_apellido ?? trim(($a->nombres ?? '').' '.($a->apellidos ?? ''));
            $grado  = $a->grado->nombre   ?? '';
            $secc   = $a->seccion->nombre ?? '';
          @endphp

          <div class="qr-card" data-nombre="{{ strtolower(trim($nombre.' '.$grado.' '.$secc)) }}">
            <div class="qr-card-accent"></div>

            <div class="qr-name">{{ $nombre }}</div>
            <div class="qr-grade">{{ $grado }} {{ $secc }}</div>

            @if($a->qr_token)
              <div class="qr-status-badge">
                <i class="bi bi-check-circle-fill"></i>
                <span>QR activo</span>
              </div>
            @else
              <div class="qr-status-badge inactive">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>Sin QR generado</span>
              </div>
            @endif

            <div class="qr-img">
              @if($a->qr_token)
                @php
                  // Construir la URL solo si tiene token
                  $path = route('asistencia.qr', ['token' => $a->qr_token], false); // /a/{token}
                  $url  = request()->getSchemeAndHttpHost() . $path;                // http://IP:puerto/a/{token}
                @endphp
                {!! QrCode::size($size)->margin(1)->generate($url) !!}
              @else
                <span class="badge-warn">Genera el QR para este alumno</span>
              @endif
            </div>

            {{-- Botón por alumno: Crear / Reemitir --}}
            <div class="qr-card-actions">
              <form method="POST" action="{{ route('alumnos.qr.emitir', $a->id) }}">
                @csrf
                @if($a->qr_token)
                  <button class="qr-btn qr-btn-outline qr-btn-sm"
                          onclick="return confirm('¿Reemitir el QR de {{ $nombre }}? El anterior dejará de ser válido.');">
                    <i class="bi bi-arrow-repeat"></i>
                    <span>Reemitir QR</span>
                  </button>
                @else
                  <button class="qr-btn qr-btn-primary qr-btn-sm"
                          onclick="return confirm('¿Crear QR para {{ $nombre }}?');">
                    <i class="bi bi-plus-circle"></i>
                    <span>Crear QR</span>
                  </button>
                @endif
              </form>
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </div>
</div>

<script>
  function filtrar(){
    const q = (document.getElementById('q').value || '').toLowerCase().trim();
    const cards = document.querySelectorAll('#grid .qr-card');
    if(!q){
      cards.forEach(c => c.style.display = '');
      return;
    }
    cards.forEach(c => {
      const hay = (c.getAttribute('data-nombre') || '').includes(q);
      c.style.display = hay ? '' : 'none';
    });
  }
</script>
@endsection
