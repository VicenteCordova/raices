<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    :root { --alto-hf: 120px; }
    .contenido-scroll{ height: calc(100vh - var(--alto-hf)); overflow-y: auto; background:#f7f7f7; }

    .sidebar-users { max-height: 65vh; overflow:auto; }
    .user-card{ cursor:pointer; transition:transform .15s ease, box-shadow .15s ease; }
    .user-card:hover{ transform:translateY(-2px); box-shadow:0 4px 10px rgba(0,0,0,.08); }
    .active-select{ background:#fff3cd!important; } /* amarillo suave */
    .estado-dot{ width:10px; height:10px; border-radius:50%; }
    .status-active{ background:#28a745; }
    .status-inactive{ background:#dc3545; }
    .avatar{ width:140px; height:140px; object-fit:cover; border-radius:16px; }
    .sticky-top-sm{ position:sticky; top:1rem; z-index:1; }
</style>

<div class="contenido-scroll">
    <div class="container-sm my-4">
        <div class="row g-3 align-items-stretch">

            <!-- ===== Sidebar ===== -->
            <aside class="col-xl-3 col-lg-4">
                <div class="card shadow-sm sticky-top-sm">
                    <div class="card-body">

                        <!--Agregar Trabajador-->
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalAdd">
                            <i class="bi bi-person-plus me-1"></i>Agregar trabajador
                        </button>

                        <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                            <h6 class="mb-0">Personal</h6>
                            <span class="badge bg-secondary" id="badgeTotal"><?= (int) ($cant_trabajadores ?? 0) ?></span>
                        </div>

                        <!-- Buscador -->
                        <input id="searchUser" type="search" class="form-control form-control-sm mb-2" placeholder="Buscar por nombre…">

                        <!-- Tabs filtros -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <button type="button" id="tabActivos"   class="btn btn-outline-secondary btn-sm active">Activos</button>
                            <button type="button" id="tabInactivos" class="btn btn-outline-secondary btn-sm">Inactivos</button>
                        </div>

                        <!-- Lista -->
                        <div id="listaUsuarios" class="list-group sidebar-users">
                            <?php
                            $usuarios = isset($usuarios) && is_array($usuarios) ? $usuarios : [];
                            if (!empty($usuarios)):
                                foreach ($usuarios as $u):
                                    // Safeguards/normalización
                                    $id = $u->IDusuario ?? $u->id ?? 0;
                                    $rut = $u->rut ?? '';
                                    $nombre = $u->nombre ?? '';
                                    $apaterno = $u->a_paterno ?? '';
                                    $amaterno = $u->a_materno ?? '';
                                    $email = $u->e_mail ?? ($u->email ?? '');
                                    $idcargo = $u->IDcargo ?? ($u->idcargo ?? '');
                                    $cargoTxt = $u->cargo ?? '';

                                    $estadoRaw = $u->estado ?? ($u->IDestado ?? ($u->idestado ?? ($u->Estado ?? null)));
                                    if ($estadoRaw === null)
                                        $estadoNum = 2;
                                    elseif (is_numeric($estadoRaw))
                                        $estadoNum = ((int) $estadoRaw === 1) ? 1 : 2;
                                    else
                                        $estadoNum = (strtolower((string) $estadoRaw) === 'activo') ? 1 : 2;
                                    $estadoClase = ($estadoNum === 1) ? 'status-active' : 'status-inactive';
                                    ?>
                                    <button type="button"
                                            class="list-group-item list-group-item-action d-flex align-items-center gap-2 user-card"
                                            data-id="<?= (int) $id ?>"
                                            data-rut="<?= html_escape($rut) ?>"
                                            data-nombre="<?= html_escape($nombre) ?>"
                                            data-apaterno="<?= html_escape($apaterno) ?>"
                                            data-amaterno="<?= html_escape($amaterno) ?>"
                                            data-email="<?= html_escape($email) ?>"
                                            data-idcargo="<?= html_escape($idcargo) ?>"
                                            data-cargo="<?= html_escape($cargoTxt) ?>"
                                            data-estado="<?= $estadoNum ?>">
                                        <div class="small flex-grow-1">
                                            <div class="fw-semibold nombre-usuario"><?= html_escape(trim($nombre . ' ' . $apaterno)) ?></div>
                                            <div class="text-muted"><?= html_escape($cargoTxt) ?></div>
                                        </div>
                                        <span class="estado-dot <?= $estadoClase ?> ms-auto"></span>
                                    </button>
                                    <?php
                                endforeach;
                            else:
                                ?>
                                <div class="list-group-item text-muted small">Sin trabajadores registrados</div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-2 small text-muted">
                            Mostrando <span id="badgeFiltrados">0</span> / <span id="badgeTotal2"><?= count($usuarios) ?></span>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- ===== Perfil ===== -->
            <main class="col-xl-9 col-lg-8">
                <div id="perfilCard" class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-center text-center text-muted" id="emptyState">
                        <div>
                            <i class="bi bi-people" style="font-size:2rem;"></i>
                            <p class="mt-2 mb-0">Selecciona un trabajador para ver y editar su perfil.</p>
                        </div>
                    </div>
                </div>
            </main>

            <!-- ===== MODAL: Agregar trabajador ===== -->
            <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form class="modal-content" method="post" action="<?= site_url('AdminUsuarios/agregar_usuario') ?>">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAddLabel">Agregar trabajador</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">RUT</label>
                                    <input type="text" class="form-control" placeholder="12345678K" name="rut" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">E-mail</label>
                                    <input type="email" class="form-control" placeholder="nombre@empresa.cl" name="e_mail" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Nombres</label>
                                    <input type="text" class="form-control" placeholder="Carlos Andrés" name="nombre" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Apellido Paterno</label>
                                    <input type="text" class="form-control" placeholder="Pérez" name="a_paterno" required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Apellido Materno</label>
                                    <input type="text" class="form-control" placeholder="Soto" name="a_materno" required>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label">Cargo</label>
                                    <select class="form-select" name="IDcargo">
                                        <?php if (!empty($cargos)): ?>
                                            <?php foreach ($cargos as $cargo): ?>
                                                <option 
                                                    value="<?= $cargo->IDcargo ?>" 
                                                    <?= ($cargo->IDcargo == 2) ? 'selected' : '' ?>>
                                                        <?= $cargo->nombre ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="">No hay cargos disponibles</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i>Guardar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    (function () {
    // ====== Datos auxiliares ======
    const cargosData = <?= json_encode(isset($cargos) && is_array($cargos) ? $cargos : []) ?>;
            // ====== DOM ======
            const lista = document.getElementById('listaUsuarios');
            const cards = Array.from(lista.querySelectorAll('.user-card'));
            const searchInput = document.getElementById('searchUser');
            const perfilCard = document.getElementById('perfilCard');
            const emptyState = document.getElementById('emptyState');
            const tabActivos = document.getElementById('tabActivos');
            const tabInactivos = document.getElementById('tabInactivos');
            const badgeFiltrados = document.getElementById('badgeFiltrados');
            const badgeTotal2 = document.getElementById('badgeTotal2');
            // Estado UI
            let estadoFiltro = 1; // 1 activos, 2 inactivos

            function renderPerfil(u, editable = false) {
            const cargoNombre = u.cargo || '';
                    const estadoTxt = (String(u.estado) === '1') ? 'Activo' : 'Inactivo';
                    const estadoChecked = (String(u.estado) === '1') ? 'checked' : '';
                    const optionsCargo = (cargosData || []).map(c =>
                    `<option value="${c.IDcargo}" ${String(c.IDcargo) === String(u.idcargo) ? 'selected' : ''}>${c.nombre}</option>`
                    ).join('');
                    perfilCard.innerHTML = `
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">Perfil del trabajador</h5>
          <button class="btn btn-outline-primary btn-sm" id="btnEditar"><i class="bi bi-pencil"></i> Editar</button>
        </div>

        <div class="row g-4">
          <div class="col-md-4 text-center">
            <img src="https://picsum.photos/seed/${u.id}/320/320" class="avatar border mb-3" alt="Foto">
            <div class="small"><span class="estado-dot ${estadoChecked ? 'status-active' : 'status-inactive'}"></span> ${estadoTxt}</div>
          </div>
          <div class="col-md-8">
            <form id="formPerfil">
              <div class="row g-3">
                <div class="col-sm-6">
                  <label class="form-label">RUT</label>
                  <input class="form-control" name="rut" value="${u.rut}" readonly>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">E-mail</label>
                  <input class="form-control" name="email" value="${u.email || ''}" readonly>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Nombres</label>
                  <input class="form-control" name="nombre" value="${u.nombre}" readonly>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Apellido paterno</label>
                  <input class="form-control" name="a_paterno" value="${u.apaterno}" readonly>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Apellido materno</label>
                  <input class="form-control" name="a_materno" value="${u.amaterno}" readonly>
                </div>
                <div class="col-sm-6">
                  <label class="form-label">Cargo</label>
                  <select class="form-select" name="IDcargo" disabled>${optionsCargo}</select>
                </div>
                <div class="col-sm-6">
                  <label class="form-label d-block">Estado</label>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="estadoSwitch" name="estado" ${estadoChecked} disabled>
                    <label class="form-check-label" for="estadoSwitch">${estadoTxt}</label>
                  </div>
                </div>
              </div>
              <input type="hidden" name="id" value="${u.id}">
              <div class="d-flex gap-2 mt-4 d-none" id="editActions">
                <button class="btn btn-success" type="submit"><i class="bi bi-save"></i> Guardar</button>
                <button class="btn btn-outline-secondary" type="button" id="btnCancelar">Cancelar</button>
              </div>
            </form>
          </div>
        </div>
      </div>`;
                    // Hook edición
                    const btnEditar = document.getElementById('btnEditar');
                    const form = document.getElementById('formPerfil');
                    const inputs = Array.from(form.querySelectorAll('input:not([readonly]), select:not([disabled])'));
                    btnEditar.addEventListener('click', () => {
                    form.querySelectorAll('input.form-control').forEach(i => {
                    if (i.name !== 'rut' && i.name !== 'email')
                            i.readOnly = false;
                    });
                            form.querySelector('select[name="IDcargo"]').disabled = false;
                            form.querySelector('#estadoSwitch').disabled = false;
                            document.getElementById('editActions').classList.remove('d-none');
                            btnEditar.classList.add('d-none');
                    });
                    form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                            const fd = new FormData(form);
                            // Enviar a tu endpoint existente:
                            const r = await fetch("<?= site_url('AdminUsuarios/editar_usuario') ?>", {method: 'POST', body: fd});
                            if (r.ok){
                    // Refrescar nombre/cargo/dot en la lista
                    const id = fd.get('id'); // obtenemos el id desde el input hidden
                            const card = document.querySelector(`.user-card[data-id="${id}"]`);
                            if (card){
                    card.dataset.nombre = fd.get('nombre');
                            card.dataset.apaterno = fd.get('a_paterno');
                            card.dataset.amaterno = fd.get('a_materno');
                            card.dataset.email = fd.get('email');
                            card.dataset.idcargo = fd.get('IDcargo');
                            const nuevoCargo = (<?= json_encode($cargos ?? []) ?>).find(c => String(c.IDcargo) === String(fd.get('IDcargo')));
                            const nombreDiv = card.querySelector('.nombre-usuario');
                            const cargoDiv = card.querySelector('.text-muted');
                            if (nombreDiv)
                            nombreDiv.textContent = `${fd.get('nombre')} ${fd.get('a_paterno')}`;
                            if (nuevoCargo) {
                    card.dataset.cargo = nuevoCargo.nombre;
                            if (cargoDiv) cargoDiv.textContent = nuevoCargo.nombre;
                    }
                    const nuevoEstado = form.querySelector('#estadoSwitch').checked ? 1 : 2;
                            card.dataset.estado = String(nuevoEstado);
                            const dot = card.querySelector('.estado-dot');
                            dot.classList.remove('status-active', 'status-inactive');
                            dot.classList.add(nuevoEstado === 1?'status-active':'status-inactive');
                            // Si cambió de activo<>inactivo, vuelve a filtrar
                            aplicarFiltro();
                    }
                    // Volver a modo lectura
                    renderPerfil({
                    ...u,
                            nombre: fd.get('nombre'),
                            apaterno: fd.get('a_paterno'),
                            amaterno: fd.get('a_materno'),
                            email: fd.get('email'),
                            idcargo: fd.get('IDcargo'),
                            cargo: card?.dataset.cargo || u.cargo,
                            estado: form.querySelector('#estadoSwitch').checked ? 1 : 2
                    });
                            Swal.fire({
                            icon: 'success',
                                    title: '¡Guardado!',
                                    text: 'Los cambios se han guardado correctamente.',
                                    showConfirmButton: false,
                                    timer: 1800
                            });
                    }
                    else{
                    Swal.fire({
                    icon: 'error',
                            title: 'Error',
                            text: 'No se pudo guardar los cambios.'
                    });
                    }
                    }
                    );
                    perfilCard.querySelector('#estadoSwitch').addEventListener('change', (ev) => {
            perfilCard.querySelector('label[for="estadoSwitch"]').textContent = ev.target.checked? 'Activo' : 'Inactivo';
            });
                    perfilCard.querySelector('#btnCancelar')?.addEventListener('click', () => renderPerfil(u));
            }

    // ====== Interacciones lista ======
    function usuarioFromCard(btn) {
    return {
    id: btn.dataset.id,
            rut: btn.dataset.rut,
            nombre: btn.dataset.nombre,
            apaterno: btn.dataset.apaterno,
            amaterno: btn.dataset.amaterno,
            email: btn.dataset.email,
            idcargo: btn.dataset.idcargo,
            cargo: btn.dataset.cargo,
            estado: btn.dataset.estado
    };
    }

    function seleccionar(btn) {
    cards.forEach(c => c.classList.remove('active-select'));
            btn.classList.add('active-select');
            emptyState && (emptyState.remove());
            renderPerfil(usuarioFromCard(btn));
    }

    lista.addEventListener('click', (e) => {
    const btn = e.target.closest('.user-card');
            if (!btn)
            return;
            seleccionar(btn);
    });
            // Filtro y búsqueda
                    function aplicarFiltro() {
                    const term = searchInput.value.trim().toLowerCase();
                            let visibles = 0;
                            cards.forEach(btn => {
                            const matchEstado = String(btn.dataset.estado) === String(estadoFiltro);
                                    const nombreCompleto = (btn.dataset.nombre + ' ' + btn.dataset.apaterno + ' ' + btn.dataset.amaterno).toLowerCase();
                                    const matchTexto = nombreCompleto.includes(term);
                                    const show = matchEstado && matchTexto;
                                    btn.classList.toggle('d-none', !show);
                                    if (show)
                                    visibles++;
                            });
                            badgeFiltrados.textContent = visibles;
                            // Autoselección si no hay seleccionado visible
                            const selected = document.querySelector('.user-card.active-select:not(.d-none)');
                            if (!selected) {
                    const first = document.querySelector('.user-card:not(.d-none)');
                            if (first)
                            seleccionar(first);
                            else
                            perfilCard.innerHTML =
                            `<div class="card-body d-flex align-items-center justify-content-center text-center text-muted">
           <div><i class="bi bi-people" style="font-size:2rem;"></i><p class="mt-2 mb-0">Sin resultados con el filtro actual.</p></div>
         </div>`;
                    }
                    }

            tabActivos.addEventListener('click', () => {
            estadoFiltro = 1;
                    tabActivos.classList.add('active');
                    tabInactivos.classList.remove('active');
                    aplicarFiltro();
            });
                    tabInactivos.addEventListener('click', () => {
                    estadoFiltro = 2;
                            tabInactivos.classList.add('active');
                            tabActivos.classList.remove('active');
                            aplicarFiltro();
                    });
                    searchInput.addEventListener('input', aplicarFiltro);
                    // Inicialización
                    badgeFiltrados.textContent = cards.length;
                    badgeTotal2.textContent = cards.length;
                    aplicarFiltro(); // también autoselecciona el primero visible
            })();
</script>
