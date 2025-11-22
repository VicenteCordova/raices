<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
  :root{ --alto-hf:120px; }
  .contenido-scroll{ height: calc(100vh - var(--alto-hf)); overflow-y:auto; background:#f7f7f7; }
  .table thead th{ white-space:nowrap; }
  .product-col{ min-width:300px; }
  .muted-badge{ background:#f1f3f5; color:#555; }
  .w-110{ width:110px; } .w-130{ width:130px; } .w-140{ width:140px; }
  .delta-badge{ font-variant-numeric: tabular-nums; }
</style>

<div class="contenido-scroll">
  <main class="container-sm my-4">

    <?php if ($this->session->flashdata('ok')): ?>
      <div class="alert alert-success"><?= $this->session->flashdata('ok') ?></div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
      <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <!-- =========================================
         A) INGRESO A BODEGA (COMPRA COMPLETA)
         ========================================= -->
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Ingreso a bodega (Compra)</h5>
          <span class="small text-muted">Guarda cabecera + detalle en una sola acción.</span>
        </div>

        <?= form_open('compras/store_full', ['id'=>'formCompra','autocomplete'=>'off']); ?>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Proveedor</label>
              <select class="form-select" name="IDproveedor" required>
                <option value="" selected disabled>Selecciona proveedor…</option>
                <?php foreach((array)$proveedores as $prov): ?>
                  <option value="<?= (int)$prov->IDproveedor ?>"><?= html_escape($prov->nombre) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Fecha</label>
              <input type="date" class="form-control" name="fecha" required
                     value="<?= date('Y-m-d') ?>">
            </div>
          </div>

          <hr class="my-3">

          <!-- Línea editable para agregar al detalle -->
          <div class="row g-2 align-items-end">
            <div class="col-md-5">
              <label class="form-label">Producto</label>
              <select class="form-select" id="selProducto">
                <option value="" selected disabled>Buscar producto…</option>
                <?php foreach((array)$productos as $p): ?>
                  <option value="<?= html_escape($p->skuProducto) ?>">
                    <?= html_escape($p->nombre) ?> (<?= html_escape($p->skuProducto) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Cantidad (kg)</label>
              <input type="number" step="0.01" min="0" class="form-control" id="inKg" placeholder="0.00">
            </div>
            <div class="col-md-2">
              <label class="form-label">Costo unit. (kg)</label>
              <input type="number" step="0.01" min="0" class="form-control" id="inPrecio" placeholder="0.00">
            </div>
            <div class="col-md-2">
              <button type="button" class="btn btn-primary w-100" id="btnAddLinea">
                <i class="bi bi-plus-circle me-1"></i>Agregar
              </button>
            </div>
          </div>

          <!-- Detalle -->
          <div class="table-responsive mt-3">
            <table class="table align-middle" id="tablaDetalle">
              <thead>
                <tr>
                  <th>Producto</th>
                  <th class="text-end">Kg</th>
                  <th class="text-end">Costo/Kg</th>
                  <th class="text-end">Subtotal</th>
                  <th class="text-center">Acción</th>
                </tr>
              </thead>
              <tbody id="detalleBody">
                <tr class="tr-empty">
                  <td colspan="5" class="text-center text-muted py-4">Sin líneas agregadas.</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <th colspan="3" class="text-end">Total</th>
                  <th class="text-end" id="tdTotal">$0</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>

          <!-- inputs ocultos del detalle -->
          <div id="hiddenInputs"></div>

          <div class="text-end">
            <button class="btn btn-success" type="submit" id="btnGuardarCompra" disabled>
              <i class="bi bi-check2-circle me-1"></i>Guardar compra en bodega
            </button>
          </div>
        <?= form_close(); ?>
      </div>
    </div>

    <!-- =========================================
         B) TRANSFORMACIÓN KILOS → BOLSAS (tu bloque)
         ========================================= -->
<!--    <div class="d-flex flex-wrap align-items-center justify-content-between mb-2">
      <h5 class="mb-2">Transformación de producto: kilos → bolsas</h5>
      <div class="d-flex align-items-center gap-2">
        <span class="badge muted-badge text-uppercase">Gramaje de venta</span>
        <span class="badge bg-warning text-dark"><?= (int)($gramaje_venta ?? 100) ?> g</span>
      </div>
    </div>

     Reutilizo tu tabla/JS de transformación (exacto como lo tenías) 
    <?php
      // Variables de apoyo
      $gramaje_venta = isset($gramaje_venta) ? (int)$gramaje_venta : 100;
      $items         = isset($items) && is_array($items) ? $items : [];
      $gramajes      = isset($gramajes) && is_array($gramajes) ? $gramajes : [50,80,100,200,250,500];
    ?>

    <div class="card shadow-sm">
      <div class="card-body">
        <?= form_open('bodega/actualizar_transformacion', ['id'=>'formTransform','autocomplete'=>'off']); ?>
          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th class="product-col">Producto</th>
                  <th class="text-center">Kilos ingresados</th>
                  <th class="text-center">Gramaje</th>
                  <th class="text-end">Procesado (g)</th>
                  <th class="text-end">Bolsas esperadas</th>
                  <th class="text-center">Bolsas reales</th>
                  <th class="text-center">Merma</th>
                  <th class="text-center">Acción</th>
                </tr>
              </thead>
              <tbody id="tbodyTransf">
                <?php if(!empty($items)): foreach($items as $i=>$it): ?>
                  <?php
                    $sku    = $it->skuProducto;
                    $nombre = $it->nombre;
                    $kilos  = (float)$it->kilos;
                  ?>
                  <tr class="row-item" data-sku="<?= html_escape($sku) ?>" data-nombre="<?= html_escape($nombre) ?>">
                    <td>
                      <strong><?= html_escape($nombre) ?></strong>
                      <div class="text-muted">SKU: <?= html_escape($sku) ?></div>
                      <input type="hidden" name="sku[]"    value="<?= html_escape($sku) ?>">
                      <input type="hidden" name="nombre[]" value="<?= html_escape($nombre) ?>">
                    </td>
                    <td class="text-center">
                      <div class="input-group input-group-sm w-130 mx-auto">
                        <input type="number" step="0.01" min="0" class="form-control kilos" name="kilos[]" value="<?= number_format($kilos,2,'.','') ?>">
                        <span class="input-group-text">kg</span>
                      </div>
                    </td>
                    <td class="text-center">
                      <select class="form-select form-select-sm gramaje w-110 mx-auto" name="gramaje[]">
                        <?php foreach($gramajes as $g): ?>
                          <option value="<?= (int)$g ?>" <?= ((int)$g===(int)$gramaje_venta ? 'selected':'') ?>><?= (int)$g ?> g</option>
                        <?php endforeach; ?>
                      </select>
                    </td>
                    <td class="text-end">
                      <span class="procesado_g">0</span>
                      <input type="hidden" name="procesado_g[]" class="procesado_g_input" value="0">
                    </td>
                    <td class="text-end">
                      <span class="esperadas fw-semibold">0</span>
                      <input type="hidden" name="esperadas[]" class="esperadas_input" value="0">
                    </td>
                    <td class="text-center">
                      <div class="input-group input-group-sm w-110 mx-auto">
                        <input type="number" min="0" step="1" class="form-control reales" name="reales[]" value="0">
                        <span class="input-group-text">bol</span>
                      </div>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-light text-dark delta-badge d-block mb-1"><span class="delta_bolsas">0</span> bol</span>
                      <small class="text-muted"><span class="delta_gramos">0</span> g</small>
                      <input type="hidden" name="merma_bolsas[]" class="merma_bolsas_input" value="0">
                      <input type="hidden" name="merma_g[]" class="merma_g_input" value="0">
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-outline-danger btn-sm btn-remove"><i class="bi bi-trash"></i></button>
                    </td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="8" class="text-center py-4 text-muted">Sin productos para procesar.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="text-end">
            <button class="btn btn-success" type="button" id="btnAbrirResumen" <?= empty($items) ? 'disabled':'' ?>
                    data-bs-toggle="modal" data-bs-target="#modalResumen" style="width:300px;">
              <i class="bi bi-arrow-up-right-circle me-1"></i>Actualizar stock de venta
            </button>
          </div>
        <?= form_close(); ?>

        <div class="form-text mt-3">
          Edita <strong>kilos ingresados</strong> y la <strong>cantidad real</strong> de bolsas. El sistema calcula lo demás.
        </div>
      </div>
    </div>-->

    <!-- Modal de resumen (transformación) -->
    <div class="modal fade" id="modalResumen" tabindex="-1" aria-labelledby="modalResumenLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header"><h5 class="modal-title" id="modalResumenLabel">Confirmar actualización de stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="border rounded p-3">
                  <div class="d-flex justify-content-between small text-muted"><span>Total kilos ingresados</span><span id="sum_kilos">0</span></div>
                  <div class="d-flex justify-content-between small text-muted"><span>Total procesado (g)</span><span id="sum_proc_g">0</span></div>
                  <div class="d-flex justify-content-between small text-muted"><span>Bolsas esperadas</span><span id="sum_esp">0</span></div>
                  <div class="d-flex justify-content-between small"><strong>Bolsas reales</strong><strong id="sum_real">0</strong></div>
                  <div class="d-flex justify-content-between small"><span class="text-muted">Merma total</span><span class="fw-semibold"><span id="sum_merma_b">0</span> bol / <span id="sum_merma_g">0</span> g</span></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                  <div class="fw-semibold mb-2">Detalle</div>
                  <div id="detalleResumen" class="small text-muted">No hay productos para actualizar.</div>
                </div>
              </div>
            </div>
            <div class="alert alert-warning mt-3 mb-0 small">
              Esta acción sumará las <strong>bolsas reales</strong> al stock de venta y descontará los gramos usados de bodega.
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-success" id="btnConfirmar">Confirmar actualización</button>
          </div>
        </div>
      </div>
    </div>

  </main>
</div>

<script>
// ====== A) COMPRA (detalle dinámico) ======
(function(){
  const sel   = document.getElementById('selProducto');
  const inKg  = document.getElementById('inKg');
  const inPr  = document.getElementById('inPrecio');
  const btn   = document.getElementById('btnAddLinea');
  const tbody = document.getElementById('detalleBody');
  const tfoot = document.getElementById('tdTotal');
  const wrapH = document.getElementById('hiddenInputs');
  const btnS  = document.getElementById('btnGuardarCompra');

  function money(n){ return '$'+(Math.round(n*100)/100).toLocaleString('es-CL'); }
  function pF(v){ const n=parseFloat(v); return isFinite(n)?n:0; }

  function recalcTotal(){
    let tot=0;
    tbody.querySelectorAll('tr.tr-item').forEach(tr=>{
      tot += pF(tr.dataset.subtotal);
    });
    tfoot.textContent = money(tot);
  }

  function toggleEmpty(){
    const empty = tbody.querySelector('.tr-empty');
    const has   = tbody.querySelector('.tr-item');
    if(empty) empty.style.display = has ? 'none' : '';
    btnS.disabled = !has;
  }

  btn.addEventListener('click', ()=>{
    const sku = sel.value || '';
    const name = sel.options[sel.selectedIndex]?.text || '';
    const kg  = pF(inKg.value);
    const pr  = pF(inPr.value);
    if (!sku || kg <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Datos incompletos',
            text: 'Selecciona un producto y una cantidad mayor a 0.',
            confirmButtonColor: '#ffcc00',
            confirmButtonText: 'Entendido'
        });
        return;
    }

    const sub = kg*pr;

    // fila visual
    const tr = document.createElement('tr');
    tr.className='tr-item';
    tr.dataset.sku = sku;
    tr.dataset.subtotal = sub;

    tr.innerHTML = `
      <td>${name}</td>
      <td class="text-end">${kg.toFixed(2)}</td>
      <td class="text-end">${money(pr)}</td>
      <td class="text-end">${money(sub)}</td>
      <td class="text-center">
        <button type="button" class="btn btn-outline-danger btn-sm btn-del"><i class="bi bi-trash"></i></button>
      </td>
    `;
    tbody.appendChild(tr);

    // inputs ocultos
    const idx = wrapH.querySelectorAll('.grp').length;
    const div = document.createElement('div');
    div.className='grp';
    div.dataset.idx=idx;
    div.innerHTML = `
      <input type="hidden" name="sku[]" value="${sku}">
      <input type="hidden" name="cantidad[]" value="${kg}">
      <input type="hidden" name="precio_unit[]" value="${pr}">
    `;
    wrapH.appendChild(div);

    tr.querySelector('.btn-del').addEventListener('click', ()=>{
      tr.remove();
      wrapH.querySelector(`.grp[data-idx="${idx}"]`)?.remove();
      recalcTotal(); toggleEmpty();
    });

    sel.value=''; inKg.value=''; inPr.value='';
    recalcTotal(); toggleEmpty();
  });

  toggleEmpty();
})();
</script>

<!-- ====== B) TRANSFORMACIÓN (tus funciones) ====== -->
<script>
(function(){
  const $tbody     = document.getElementById('tbodyTransf');
  const $form      = document.getElementById('formTransform');
  const $btnAbrir  = document.getElementById('btnAbrirResumen');
  const $btnConf   = document.getElementById('btnConfirmar');

  const $sum_kilos   = document.getElementById('sum_kilos');
  const $sum_proc_g  = document.getElementById('sum_proc_g');
  const $sum_esp     = document.getElementById('sum_esp');
  const $sum_real    = document.getElementById('sum_real');
  const $sum_merma_b = document.getElementById('sum_merma_b');
  const $sum_merma_g = document.getElementById('sum_merma_g');
  const $detalle     = document.getElementById('detalleResumen');

  function pF(v, d=0){ const n = parseFloat(v); return isFinite(n) ? n : d; }
  function pI(v, d=0){ const n = parseInt(v,10); return isFinite(n) ? n : d; }
  function fmt(n){ return (Math.round(n)).toString(); }
  function fmt2(n){ return (Math.round(n*100)/100).toFixed(2); }

  function recalcRow(tr){
    const $kg  = tr.querySelector('.kilos');
    const $gr  = tr.querySelector('.gramaje');
    const $rl  = tr.querySelector('.reales');

    const $esperadasSpan  = tr.querySelector('.esperadas');
    const $esperadasInput = tr.querySelector('.esperadas_input');
    const $procSpan       = tr.querySelector('.procesado_g');
    const $procInput      = tr.querySelector('.procesado_g_input');

    const $deltaBolsas    = tr.querySelector('.delta_bolsas');
    const $deltaGramos    = tr.querySelector('.delta_gramos');
    const $mermaBolInput  = tr.querySelector('.merma_bolsas_input');
    const $mermaGInput    = tr.querySelector('.merma_g_input');

    const kg      = pF($kg.value, 0);
    const total_g = kg * 1000;
    const gr      = pI($gr.value, 100);
    const reales  = pI($rl.value, 0);

    const esperadas   = Math.floor(total_g / gr);
    const procesado_g = esperadas * gr;

    const delta_b     = reales - esperadas;
    const delta_g     = (reales * gr) - procesado_g;

    $esperadasSpan.textContent = fmt(esperadas);
    $esperadasInput.value      = esperadas;
    $procSpan.textContent      = fmt(procesado_g);
    $procInput.value           = procesado_g;

    $deltaBolsas.textContent   = (delta_b >= 0 ? '+' : '') + fmt(delta_b);
    $deltaGramos.textContent   = (delta_g >= 0 ? '+' : '') + fmt(delta_g);
    $mermaBolInput.value       = delta_b;
    $mermaGInput.value         = delta_g;

    const badge = tr.querySelector('.delta-badge');
    badge.classList.remove('bg-light','text-dark','bg-danger','bg-success','text-white');
    if (delta_b < 0){
      badge.classList.add('bg-danger','text-white');
    } else if (delta_b > 0){
      badge.classList.add('bg-success','text-white');
    } else {
      badge.classList.add('bg-light','text-dark');
    }
  }

  function bindRow(tr){
    ['input','change'].forEach(ev=>{
      tr.querySelector('.kilos')?.addEventListener(ev, ()=>recalcRow(tr));
      tr.querySelector('.gramaje')?.addEventListener(ev, ()=>recalcRow(tr));
      tr.querySelector('.reales')?.addEventListener(ev, ()=>recalcRow(tr));
    });
    tr.querySelector('.btn-remove')?.addEventListener('click', ()=>{
      tr.parentNode.removeChild(tr);
      if(!$tbody.querySelector('.row-item')){ $btnAbrir.disabled = true; }
      recalcTotals();
    });
    recalcRow(tr);
  }

  function recalcTotals(){
    let sumKg=0, sumProc=0, sumEsp=0, sumReal=0, sumMerB=0, sumMerG=0;
    let lines = [];

    $tbody.querySelectorAll('.row-item').forEach(tr=>{
      const sku   = tr.dataset.sku || '';
      const name  = tr.dataset.nombre || '';

      const kg    = pF(tr.querySelector('.kilos').value, 0);
      const gr    = pI(tr.querySelector('.gramaje').value, 100);
      const esp   = pI(tr.querySelector('.esperadas_input').value, 0);
      const proc  = pI(tr.querySelector('.procesado_g_input').value, 0);
      const real  = pI(tr.querySelector('.reales').value, 0);
      const mb    = pI(tr.querySelector('.merma_bolsas_input').value, 0);
      const mg    = pI(tr.querySelector('.merma_g_input').value, 0);

      sumKg   += kg; sumProc += proc; sumEsp += esp; sumReal += real; sumMerB += mb; sumMerG += mg;

      lines.push(`<div class="d-flex justify-content-between"><span>${name} <small class="text-muted">(${sku})</small></span><span>${real} bol @ ${gr} g</span></div>`);
    });

    document.getElementById('sum_kilos').textContent   = fmt2(sumKg);
    document.getElementById('sum_proc_g').textContent  = fmt(sumProc);
    document.getElementById('sum_esp').textContent     = fmt(sumEsp);
    document.getElementById('sum_real').textContent    = fmt(sumReal);
    document.getElementById('sum_merma_b').textContent = (sumMerB >= 0 ? '+' : '') + fmt(sumMerB);
    document.getElementById('sum_merma_g').textContent = (sumMerG >= 0 ? '+' : '') + fmt(sumMerG);

    document.getElementById('detalleResumen').innerHTML = lines.length ? lines.join('') : 'No hay productos para actualizar.';
  }

  $tbody.querySelectorAll('.row-item').forEach(bindRow);
  $btnAbrir?.addEventListener('click', recalcTotals);
  $btnConf?.addEventListener('click', function(){
    let ok = true;
    $tbody.querySelectorAll('.row-item').forEach(tr=>{
      const kg   = pF(tr.querySelector('.kilos').value, 0);
      const real = pI(tr.querySelector('.reales').value, 0);
      if(real > 0 && kg === 0){ ok=false; }
    });
    if (!ok) {
        Swal.fire({
            icon: 'error',
            title: 'Datos inconsistentes',
            text: 'Hay filas con “bolsas reales” mayores a 0 pero kilos = 0. Revisa los datos antes de continuar.',
            confirmButtonColor: '#ffcc00',
            confirmButtonText: 'Entendido'
    });
    return;
    }
    document.getElementById('formTransform').submit();
  });
})();
</script>
