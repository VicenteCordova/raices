<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
  /* Scroll solo en el cuerpo (entre header y footer) */
  :root{ --alto-hf:120px; } /* ajusta según alto combinado de header+footer */
  .contenido-scroll{
    height: calc(100vh - var(--alto-hf));
    overflow-y: auto;
    background:#f7f7f7;
  }

  /* Estilos visuales del reporte */
  .sidebar-reports { max-height: 62vh; overflow:auto; }
  .rep-active { background:#ffe082 !important; }
  .summary-chip { background:#f1f3f5; border-radius:999px; padding:.35rem .75rem; display:inline-block; }
  .avatar { width:56px; height:56px; border-radius:12px; object-fit:cover; }
  .table thead th { white-space: nowrap; }
  .filter-card { position: sticky; top: 84px; z-index: 9; }
</style>

<div class="contenido-scroll">
  <div class="container-sm my-4">
    <div class="row g-3">

      <!-- ===== Sidebar: tipo de reporte (visual) ===== -->
      <aside class="col-md-3 d-none d-md-block">
        <div class="card shadow-sm">
          <div class="card-body">
            <h6 class="mb-3">Tipo de reporte</h6>
            <div class="list-group sidebar-reports">
              <button class="list-group-item list-group-item-action rep-active">
                <i class="bi bi-calendar-day me-2"></i> Diario
              </button>
              <button class="list-group-item list-group-item-action">
                <i class="bi bi-calendar-week me-2"></i> Semanal (Lun–Dom)
              </button>
              <button class="list-group-item list-group-item-action">
                <i class="bi bi-calendar-month me-2"></i> Mensual
              </button>
            </div>
          </div>
        </div>
      </aside>

      <!-- ===== Panel principal ===== -->
      <main class="col-md-9">
        <!-- Tarjeta: vendedor conectado (placeholders) -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
              <img class="avatar border" src="https://picsum.photos/seed/juan/200/200" alt="Foto vendedor">
              <div>
                <div class="fw-semibold">Juan Pérez</div>
                <div class="small text-muted">Rol: Vendedor · ID: <span>VND-001</span></div>
                <div class="small text-muted">Este reporte pertenece al usuario autenticado.</div>
              </div>
            </div>
            <div class="d-flex flex-wrap align-items-center gap-2">
              <span class="summary-chip"><span class="text-muted">Ventas: </span><strong>0</strong></span>
              <span class="summary-chip"><span class="text-muted">Unidades: </span><strong>0</strong></span>
              <span class="summary-chip"><span class="text-muted">Total: </span><strong>$0</strong></span>
            </div>
          </div>
        </div>

        <!-- Filtros (solo UI) -->
        <div class="card border-0 shadow-sm mb-3 filter-card">
          <div class="card-body">
            <div class="row g-3 align-items-end">
              <div class="col-12 col-sm-6 col-lg-4">
                <label class="form-label">Selecciona día</label>
                <input type="date" class="form-control">
              </div>
              <div class="col-12 col-sm-6 col-lg-5 d-none">
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label">Selecciona semana (Lun–Dom)</label>
                    <input type="date" class="form-control">
                  </div>
                  <div class="col-12">
                    <div class="small text-muted">Rango: —</div>
                  </div>
                </div>
              </div>
              <div class="col-12 col-sm-6 col-lg-4 d-none">
                <label class="form-label">Selecciona mes</label>
                <input type="month" class="form-control">
                <div class="form-text">Año por defecto: actual.</div>
              </div>

              <div class="col-12 col-sm-auto ms-auto">
                <button class="btn btn-primary w-100" type="button">
                  <i class="bi bi-search me-1"></i>Buscar
                </button>
              </div>
              <div class="col-12 col-sm-auto">
                <button class="btn btn-outline-success w-100" type="button">
                  <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabla (estado vacío visual) -->
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>Venta</th>
                    <th>Fecha</th>
                    <th class="text-end">Unidades</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="5">
                      <div class="text-center py-5">
                        <i class="bi bi-receipt-cutoff" style="font-size:2rem;"></i>
                        <p class="mt-2 mb-0">No hay ventas en el periodo seleccionado.</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
</div>
