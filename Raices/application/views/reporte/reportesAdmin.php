<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
  /* ===== Scroll solo en el área central ===== */
  :root{ --alto-hf:120px; } /* ajusta si header+footer ocupan más/menos */
  .contenido-scroll{
    height: calc(100vh - var(--alto-hf));
    overflow-y: auto;
    background:#f7f7f7;
  }

  /* ===== Estilos de la página ===== */
  .sidebar-reports { max-height: 62vh; overflow:auto; }
  .rep-active { background:#ffe082 !important; }
  .summary-chip { background:#f1f3f5; border-radius:999px; padding:.35rem .75rem; display:inline-block; }
  .table thead th { white-space: nowrap; }
  .filter-card { position: sticky; top: 84px; z-index: 9; }

  .collapse-inner .list-group-item { border:0; padding-left: 2.25rem; }
  .collapse-header { font-size:.8rem; color:#6c757d; margin:.25rem 0 .5rem 2.25rem; }
  .pill { border:1px solid #dee2e6; border-radius:999px; padding:.2rem .6rem; cursor:pointer; }
  .pill.active { background:#212529; color:#fff; border-color:#212529; }
</style>

<div class="contenido-scroll">
  <div class="container-sm my-4">
    <div class="row g-3">

      <!-- ===== SIDEBAR (desktop) ===== -->
      <aside class="col-md-3 d-none d-md-block">
        <div class="card shadow-sm">
          <div class="card-body">
            <h6 class="mb-3">Reportes</h6>

            <div class="list-group sidebar-reports" id="leftMenu">
              <!-- Ventas -->
              <button class="list-group-item d-flex justify-content-between align-items-center rep-item rep-active" data-section="ventas">
                <span><i class="bi bi-bag-check me-2"></i>Ventas</span>
                <i class="bi bi-caret-down-fill small"></i>
              </button>
              <div class="collapse show" id="ventasCollapse">
                <div class="collapse-header">Periodo</div>
                <div class="list-group collapse-inner">
                  <button class="list-group-item rep-subitem rep-active" data-section="ventas" data-mode="diario"><i class="bi bi-calendar-day me-2"></i>Diario</button>
                  <button class="list-group-item rep-subitem" data-section="ventas" data-mode="semanal"><i class="bi bi-calendar-week me-2"></i>Semanal (Lun–Dom)</button>
                  <button class="list-group-item rep-subitem" data-section="ventas" data-mode="mensual"><i class="bi bi-calendar-month me-2"></i>Mensual</button>
                </div>
                <div class="collapse-header">Alcance</div>
                <div class="d-flex gap-2 flex-wrap ps-4 pb-3">
                  <span class="pill active" data-scope="total">Total empresa</span>
                  <span class="pill" data-scope="vendedor">Por vendedor</span>
                </div>
              </div>

              <!-- Bodega -->
              <button class="list-group-item d-flex justify-content-between align-items-center rep-item" data-section="bodega">
                <span><i class="bi bi-box-seam me-2"></i>Bodega</span>
                <i class="bi bi-caret-right-fill small"></i>
              </button>
              <div class="collapse" id="bodegaCollapse">
                <div class="collapse-header">Periodo</div>
                <div class="list-group collapse-inner">
                  <button class="list-group-item rep-subitem" data-section="bodega" data-mode="diario"><i class="bi bi-calendar-day me-2"></i>Diario</button>
                  <button class="list-group-item rep-subitem" data-section="bodega" data-mode="semanal"><i class="bi bi-calendar-week me-2"></i>Semanal</button>
                  <button class="list-group-item rep-subitem" data-section="bodega" data-mode="mensual"><i class="bi bi-calendar-month me-2"></i>Mensual</button>
                </div>
              </div>

              <!-- Estadísticas -->
              <button class="list-group-item d-flex justify-content-between align-items-center rep-item" data-section="stats">
                <span><i class="bi bi-graph-up-arrow me-2"></i>Estadísticas</span>
                <i class="bi bi-caret-right-fill small"></i>
              </button>
              <div class="collapse" id="statsCollapse">
                <div class="collapse-header">Periodo</div>
                <div class="list-group collapse-inner">
                  <button class="list-group-item rep-subitem" data-section="stats" data-mode="diario"><i class="bi bi-calendar-day me-2"></i>Diario</button>
                  <button class="list-group-item rep-subitem" data-section="stats" data-mode="semanal"><i class="bi bi-calendar-week me-2"></i>Semanal</button>
                  <button class="list-group-item rep-subitem" data-section="stats" data-mode="mensual"><i class="bi bi-calendar-month me-2"></i>Mensual</button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </aside>

      <!-- ===== OFFCANVAS (móvil) ===== -->
      <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasRep" aria-labelledby="offcanvasRepLabel">
        <div class="offcanvas-header">
          <h5 class="offcanvas-title" id="offcanvasRepLabel">Reportes</h5>
          <button class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
          <div id="leftMenuMobile"><!-- se clona por JS --></div>
        </div>
      </div>

      <!-- ===== PANEL PRINCIPAL ===== -->
      <main class="col-md-9">

        <!-- Encabezado & resumen -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
              <div class="fw-semibold" id="headerTitle">Ventas — Diario · Total empresa</div>
              <div class="small text-muted">Rango actual: <span id="headerRange">—</span></div>
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
              <div class="col-12 col-sm-6 col-lg-4" id="filterDaily">
                <label class="form-label">Selecciona día</label>
                <input type="date" class="form-control">
              </div>

              <div class="col-12 col-sm-6 col-lg-5 d-none" id="filterWeekly">
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

              <div class="col-12 col-sm-6 col-lg-4 d-none" id="filterMonthly">
                <label class="form-label">Selecciona mes</label>
                <input type="month" class="form-control">
                <div class="form-text">Año por defecto: actual.</div>
              </div>

              <div class="col-12 col-sm-6 col-lg-3 d-none" id="filterVendedor">
                <label class="form-label">Vendedor</label>
                <select class="form-select" disabled>
                  <option>—</option>
                </select>
              </div>

              <div class="col-12 col-sm-auto ms-auto">
                <button class="btn btn-primary w-100" type="button"><i class="bi bi-search me-1"></i>Buscar</button>
              </div>
              <div class="col-12 col-sm-auto">
                <button class="btn btn-outline-success w-100" type="button"><i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Contenedores (solo visual, vacíos) -->
        <div class="card border-0 shadow-sm mb-3" id="cardVentas">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th># Venta</th>
                    <th>Fecha</th>
                    <th>Vendedor</th>
                    <th class="text-end">Unidades</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="6">
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

        <div class="card border-0 shadow-sm mb-3 d-none" id="cardBodega">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table align-middle">
                <thead>
                  <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th class="text-end">Kilos</th>
                    <th class="text-end">Ideal (g)</th>
                    <th class="text-end">Real (g)</th>
                    <th>Ingresado por</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="6">
                      <div class="text-center py-5">
                        <i class="bi bi-box-seam" style="font-size:2rem;"></i>
                        <p class="mt-2 mb-0">No hay registros de bodega para el periodo.</p>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="card border-0 shadow-sm d-none" id="cardStats">
          <div class="card-body">
            <div class="row g-4">
              <div class="col-12 col-lg-6">
                <h6>Top productos más vendidos</h6>
                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead><tr><th>#</th><th>Producto</th><th class="text-end">Unidades</th></tr></thead>
                    <tbody><tr><td colspan="3" class="text-center py-4 text-muted">Sin datos</td></tr></tbody>
                  </table>
                </div>
              </div>
              <div class="col-12 col-lg-6">
                <h6>Productos menos vendidos</h6>
                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead><tr><th>#</th><th>Producto</th><th class="text-end">Unidades</th></tr></thead>
                    <tbody><tr><td colspan="3" class="text-center py-4 text-muted">Sin datos</td></tr></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

      </main>

    </div>
  </div>
</div>

<!-- ===== Modal (solo UI) ===== -->
<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="modalDetalleLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detalle de venta <span class="text-muted">#—</span></h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Producto</th>
                <th class="text-end">Cant.</th>
                <th class="text-end">P. Unit.</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="4" class="text-center py-4 text-muted">Sin items</td></tr>
            </tbody>
          </table>
        </div>
        <div class="d-flex justify-content-end">
          <div class="summary-chip"><span class="text-muted">Total: </span><strong>$0</strong></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>
  // Clonar el menú al offcanvas móvil (solo visual)
  document.getElementById('leftMenuMobile').innerHTML =
    document.getElementById('leftMenu').outerHTML;
</script>
