<style>
    /* ================================
       🌙 MODO OSCURO PREMIUM
       ================================ */

    .modal-content {
        border-radius: 22px !important;
        overflow: hidden;
        background: #0f172a !important;
        border: 1px solid rgba(148,163,184,0.20);
        box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        animation: fadeIn .25s ease-out;
    }

    @keyframes fadeIn {
        from { transform: translateY(-10px); opacity: 0; }
        to   { transform: translateY(0); opacity: 1; }
    }

    .modal-header {
        background: linear-gradient(135deg, #4c1d95, #1e3a8a);
        border-bottom: none;
        padding: 18px 22px;
    }

    .modal-title {
        font-weight: 700;
        color: #f8fafc;
    }

    .modal-body {
        background: radial-gradient(circle at top left, #1e293b 0%, #0f172a 90%);
        padding: 24px;
        color: #e2e8f0;
    }

    /* ====== BOTONES NEÓN ====== */
    .soft-btn {
        width: 100%;
        border: none;
        padding: 14px;
        font-size: 1rem;
        border-radius: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all .18s ease;
        background: #1e293b;
        color: #f1f5f9;
        border: 1px solid rgba(255,255,255,0.08);
        box-shadow: 0 8px 24px rgba(0,0,0,0.5);
    }

    .soft-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 14px 30px rgba(0,0,0,0.7);
        filter: brightness(1.15);
    }

    .btn-excel {
        background: linear-gradient(135deg,#047857,#10b981);
        box-shadow: 0 10px 20px rgba(16,185,129,0.28);
    }

    .btn-csv {
        background: linear-gradient(135deg,#475569,#64748b);
        box-shadow: 0 10px 20px rgba(100,116,139,0.28);
    }

    .btn-pdf {
        background: linear-gradient(135deg,#b91c1c,#ef4444);
        box-shadow: 0 10px 20px rgba(239,68,68,0.28);
    }

    .btn-print {
        background: linear-gradient(135deg,#ca8a04,#facc15);
        box-shadow: 0 10px 20px rgba(250,204,21,0.28);
    }

    /* ====== CHECKS ====== */
    #modalGrados label {
        font-size: .95rem;
        font-weight: 600;
        color: #f1f5f9;
        margin-top: 8px;
        margin-bottom: 3px;
        display: block;
    }

    #modalGrados input[type="checkbox"] {
        transform: scale(1.25);
        accent-color: #6366f1;
        margin-right: 6px;
    }

    #modalGrados .modal-body {
        max-height: 60vh;
        overflow-y: auto;
    }

    /* ====== FECHA VISIBLE ====== */
    .dark-date-input {
        background: #0f172a !important;
        color: #f8fafc !important;
        border: 1px solid #334155 !important;
        padding: 10px 14px;
        border-radius: 10px;
    }

    .dark-date-input:focus {
        outline: none !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 2px rgba(99,102,241,0.45) !important;
    }

    .dark-date-input::-webkit-calendar-picker-indicator {
        filter: invert(1) brightness(1.8);
    }

</style>

{{-- ============================
      MODAL 1 - Seleccionar Formato
   ============================ --}}
<div class="modal fade" id="modalFormatos" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow">

            <div class="modal-header">
                <h5 class="modal-title">Seleccionar formato</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                <button class="soft-btn btn-excel mb-2"
                        data-formato="excel"
                        data-bs-toggle="modal"
                        data-bs-target="#modalGrados">
                    <i class="bi bi-file-earmark-excel-fill"></i> Exportar a Excel
                </button>

                <button class="soft-btn btn-csv mb-2"
                        data-formato="csv"
                        data-bs-toggle="modal"
                        data-bs-target="#modalGrados">
                    <i class="bi bi-filetype-csv"></i> Exportar a CSV
                </button>

                <button class="soft-btn btn-pdf mb-2"
                        data-formato="pdf"
                        data-bs-toggle="modal"
                        data-bs-target="#modalGrados">
                    <i class="bi bi-file-earmark-pdf-fill"></i> Exportar a PDF
                </button>

                <button class="soft-btn btn-print mb-2"
                        data-formato="impresora"
                        data-bs-toggle="modal"
                        data-bs-target="#modalGrados">
                    <i class="bi bi-printer-fill"></i> Imprimir
                </button>

            </div>

        </div>
    </div>
</div>

{{-- ============================
      MODAL 2 - Filtros
   ============================ --}}
<div class="modal fade" id="modalGrados" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content shadow">

            <div class="modal-header bg-dark">
                <h5 class="modal-title text-white">Filtrar reporte</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('asistencias.exportar') }}" method="GET">

                <div class="modal-body">

                    <input type="hidden" name="formato" id="formatoSeleccionado">

                    <label class="fw-bold">📅 Fecha:</label>
                    <input type="date" name="fecha" class="form-control mb-3 dark-date-input"
                           value="{{ date('Y-m-d') }}" required>

                    <label class="fw-bold mt-2">🎓 Grados:</label>
                    @foreach($gradosGlobal as $g)
                        <div><input type="checkbox" name="grados[]" value="{{ $g->id }}"> {{ $g->nombre }}</div>
                    @endforeach

                    <hr class="text-white">

                    <label class="fw-bold mt-2">🏫 Secciones:</label>
                    @foreach($seccionesGlobal as $s)
                        <div><input type="checkbox" name="secciones[]" value="{{ $s->id }}"> {{ $s->nombre }}</div>
                    @endforeach

                </div>

                <div class="modal-footer">
                    <button class="soft-btn btn-excel" type="submit">
                        <i class="bi bi-check-circle-fill"></i> Generar Reporte
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-formato]').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('formatoSeleccionado').value = this.dataset.formato;
    });
});
</script>
