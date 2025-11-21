<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Control de Asistencias</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body{
      background: radial-gradient(circle at top left,#1e293b 0,#020617 55%,#020617 100%);
      overflow-x:hidden;
      font-family:"Inter",sans-serif;
      color:#e5e7eb;
      transition:background .3s ease;
    }

    /* ===== SIDEBAR ESTILO DRIBBBLE ===== */
    .sidebar{
      position:fixed;
      top:0; left:0;
      width:260px; height:100vh;
      background:linear-gradient(180deg,#020617 0%,#020617 40%,#0b1120 100%);
      color:#e5e7eb;
      display:flex; flex-direction:column;
      justify-content:space-between;
      padding:22px 18px;
      box-shadow: 22px 0 45px rgba(15,23,42,0.75);
      z-index:1000;
      transition:transform .4s ease;
      border-right:1px solid rgba(148,163,184,0.35);
    }
    .sidebar.hidden{ transform:translateX(-100%); }

    .sidebar-header{
      display:flex;
      flex-direction:column;
      gap:10px;
      margin-bottom:14px;
    }

    .brand-row{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:10px;
    }

    .brand-badge{
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:42px; height:42px;
      border-radius:18px;
      background:radial-gradient(circle at 20% 0%,#4f46e5,#a855f7);
      box-shadow:0 10px 30px rgba(79,70,229,0.7);
      font-weight:800;
      font-size:1rem;
      color:#f9fafb;
    }

    .brand-text{
      display:flex;
      flex-direction:column;
      line-height:1.2;
    }
    .brand-title{
      font-size:0.95rem;
      font-weight:700;
      letter-spacing:0.04em;
      text-transform:uppercase;
      color:#e5e7eb;
    }
    .brand-sub{
      font-size:0.75rem;
      color:#9ca3af;
    }

    .user-mini{
      display:flex;
      align-items:center;
      gap:10px;
      padding:8px 10px;
      border-radius:999px;
      background:rgba(15,23,42,0.9);
      border:1px solid rgba(75,85,99,0.6);
    }
    .user-avatar{
      width:28px; height:28px;
      border-radius:999px;
      background:#1d4ed8;
      display:flex; align-items:center; justify-content:center;
      font-size:0.9rem; font-weight:700;
      color:#e5e7eb;
    }
    .user-name{
      font-size:0.8rem;
      font-weight:600;
      color:#e5e7eb;
    }

    .menu-label{
      font-size:0.75rem;
      font-weight:600;
      text-transform:uppercase;
      letter-spacing:0.12em;
      color:#6b7280;
      margin:14px 0 6px 4px;
    }

    .menu{
      padding-top:6px;
      padding-bottom:100px;
    }

    .menu a{
      display:flex; align-items:center; gap:10px;
      color:#d1d5db; text-decoration:none;
      padding:9px 12px;
      border-radius:999px;
      margin-bottom:6px;
      font-size:0.9rem;
      transition:all .2s ease;
      position:relative;
    }

    .menu a i{
      font-size:1.05rem;
    }

    .menu a::before{
      content:'';
      position:absolute;
      inset:0;
      border-radius:999px;
      opacity:0;
      background:radial-gradient(circle at 0 0,#4f46e5 0,transparent 60%);
      transition:opacity .2s ease;
      pointer-events:none;
    }

    .menu a span{
      position:relative;
      z-index:1;
    }

    .menu a:hover{
      color:#f9fafb;
      background:rgba(31,41,55,0.9);
    }
    .menu a:hover::before{
      opacity:0.6;
    }

    .menu a.active{
      background:linear-gradient(135deg,#6366f1,#a855f7);
      color:#f9fafb;
      box-shadow:0 12px 35px rgba(79,70,229,0.85);
    }
    .menu a.active::before{
      opacity:0;
    }

    .logout-btn{
      background:rgba(15,23,42,0.95);
      color:#e5e7eb;
      border:1px solid rgba(148,163,184,0.55);
      width:100%;
      padding:8px 0;
      border-radius:999px;
      font-weight:600;
      font-size:0.9rem;
      transition:all .2s ease;
      display:flex;
      align-items:center;
      justify-content:center;
      gap:8px;
      margin-top:8px;
    }
    .logout-btn:hover{
      background:#ef4444;
      border-color:#f97373;
      color:#fef2f2;
      box-shadow:0 10px 30px rgba(239,68,68,0.8);
    }

    /* ===== MAIN ===== */
    main{
      margin-left:260px;
      padding:32px;
      transition:margin-left .4s ease;
      min-height:100vh;
    }
    .sidebar.hidden ~ main{
      margin-left:0;
    }

    /* ===== TOGGLE ===== */
    .menu-toggle{
      position:fixed;
      top:16px; left:16px;
      background:rgba(15,23,42,0.95);
      color:#e5e7eb;
      border:1px solid rgba(75,85,99,0.9);
      font-size:22px;
      border-radius:14px;
      padding:6px 10px;
      z-index:1100;
      cursor:pointer;
      transition:background .2s, transform .15s;
      box-shadow:0 10px 25px rgba(15,23,42,0.8);
    }
    .menu-toggle:hover{
      background:#111827;
      transform:translateY(-1px);
    }

    /* ===== Responsive ===== */
    @media (max-width:992px){
      .sidebar{
        transform:translateX(-100%);
      }
      .sidebar:not(.hidden){
        transform:translateX(0);
      }
      main{
        margin-left:0;
        padding:20px;
      }
    }
  </style>
</head>

<body class="route-{{ str_replace('.', '-', Route::currentRouteName()) }}">

  {{-- Botón hamburguesa --}}
  <button class="menu-toggle" id="menuToggle">
    <i class="bi bi-list"></i>
  </button>

  {{-- SIDEBAR --}}
  <div class="sidebar" id="sidebarMenu">
    <div>
      <div class="sidebar-header">
        <div class="brand-row">
          <div class="brand-badge">
            FP
          </div>
          <div class="brand-text">
            <span class="brand-title">Asistencia</span>
            <span class="brand-sub">Florentino Portugal</span>
          </div>
        </div>

        <div class="user-mini mt-2">
          <div class="user-avatar">
            {{ strtoupper(substr(Auth::user()->name ?? 'JD', 0, 2)) }}
          </div>
          <div class="user-name">
            {{ Auth::user()->name ?? 'Usuario' }}
          </div>
        </div>
      </div>

      <div class="menu-label">Menú principal</div>
      <nav class="menu">
        <a href="{{ route('alumnos.index') }}"
        class="{{ request()->routeIs('alumnos.index') ? 'active' : '' }}">
          <i class="bi bi-grid"></i><span>Inicio</span>
      </a>



    <a href="{{ route('alumnos.seleccionar') }}"
   class="{{ request()->routeIs('alumnos.*')
            && !request()->routeIs('alumnos.index')
            && !request()->routeIs('alumnos.credenciales') ? 'active' : '' }}">
    <i class="bi bi-people"></i><span>Alumnos</span>
</a>



        <a href="{{ route('asistencias.seleccionar') }}" class="{{ request()->routeIs('asistencias.seleccionar') ? 'active' : '' }}">
          <i class="bi bi-clipboard-check"></i><span>Asistencias</span>
        </a>

        <a href="{{ route('asistencias.agregarSeleccionar') }}" class="{{ request()->routeIs('asistencias.agregarSeleccionar') ? 'active' : '' }}">
          <i class="bi bi-clipboard-plus"></i><span>Agregar asistencia</span>
        </a>

        <a href="{{ route('estadisticas.index') }}" class="{{ request()->routeIs('estadisticas.*') ? 'active' : '' }}">
          <i class="bi bi-bar-chart"></i><span>Estadísticas</span>
        </a>

       <a href="{{ route('alumnos.credenciales') }}"
        class="{{ request()->routeIs('alumnos.credenciales') ? 'active' : '' }}">
          <i class="bi bi-qr-code-scan"></i><span>Credenciales QR</span>
      </a>


        @auth
          @if((auth()->user()->role ?? null) === 'admin')
            <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
              <i class="bi bi-shield-lock"></i><span>Usuarios</span>
            </a>
          @endif
        @endauth
      </nav>
    </div>

    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="logout-btn">
        <i class="bi bi-box-arrow-right"></i>
        <span>Salir</span>
      </button>
    </form>
  </div>

  {{-- CONTENIDO PRINCIPAL --}}
  <main>
    @yield('content')
  </main>

  {{-- JS de Bootstrap --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  {{-- Script del menú lateral --}}
  <script>
    const sidebar = document.getElementById('sidebarMenu');
    const toggleBtn = document.getElementById('menuToggle');

    toggleBtn?.addEventListener('click', () => {
      sidebar.classList.toggle('hidden');
    });

    // Cerrar al tocar fuera en móvil
    document.addEventListener('click', (e) => {
      const isMobile = window.matchMedia('(max-width: 992px)').matches;
      if (!isMobile) return;
      if (sidebar && !sidebar.contains(e.target) && e.target !== toggleBtn) {
        sidebar.classList.add('hidden');
      }
    });
  </script>

  {{-- Toast de éxito --}}
  @if(session('success'))
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const toast = document.createElement('div');
        toast.textContent = @json(session('success'));
        Object.assign(toast.style, {
          position: "fixed", bottom: "30px", right: "30px",
          background: "#16a34a", color: "#fff",
          padding: "15px 25px", borderRadius: "12px",
          fontWeight: "600", boxShadow: "0 4px 10px rgba(0,0,0,.2)",
          zIndex: 9999, transition: "opacity .5s"
        });
        document.body.appendChild(toast);
        setTimeout(() => toast.style.opacity = "0", 3000);
        setTimeout(() => toast.remove(), 3500);
      });
    </script>
  @endif

  {{-- Scripts extra de las vistas (Chart.js, etc.) --}}
  @stack('scripts')
</body>
</html>
