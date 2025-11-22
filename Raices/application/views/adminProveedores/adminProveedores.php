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
    <div class="container-sm my-4 position-relative">

        <!-- Título -->
        <h4 class="mb-4">Gestión de Proveedores</h4>

        <!-- Barra de búsqueda -->
        <div class="mb-3">
            <input type="text" id="busqueda" class="form-control" placeholder="Buscar proveedor por nombre, RUT o correo...">
        </div>

        <!-- Filtro de estado -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <label for="filtroEstado" class="form-label fw-semibold mb-0">Filtrar por estado:</label>
                <select id="filtroEstado" class="form-select w-auto">
                    <option value="todos" selected>Todos</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>

            <button class="btn btn-primary btn-agregar" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="bi bi-person-plus"></i> Agregar Proveedor
            </button>
        </div>

        <!-- Tabla de proveedores -->
        <div class="card shadow-sm">
            <div class="card-body table-responsive">
                <?php if (empty($proveedores)): ?>
                    <p class="text-muted text-center my-3">No hay proveedores registrados.</p>
                <?php else: ?>
                    <table class="table align-middle" id="tablaProveedores">
                        <thead class="table-light">
                            <tr>
                                <th>RUT</th>
                                <th>Nombre</th>
                                <th>E-mail</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($proveedores as $prov): ?>
                                <?php
                                $idEstado = $prov->IDestado ?? 2; // por defecto inactivo
                                $estadoTexto = ($idEstado == 1) ? 'Activo' : 'Inactivo';
                                $estadoClase = ($idEstado == 1) ? 'text-success' : 'text-danger';
                                ?>
                                <tr data-estado="<?= strtolower($estadoTexto) ?>">
                                    <td><?= html_escape($prov->rut) ?></td>
                                    <td><?= html_escape($prov->nombre) ?></td>
                                    <td><?= html_escape($prov->email) ?></td>
                                    <td><?= html_escape($prov->telefono) ?></td>
                                    <td class="<?= $estadoClase ?> fw-semibold"><?= $estadoTexto ?></td>
                                    <td class="text-center">
                                        <button 
                                            class="btn btn-outline-primary btn-sm btn-editar"
                                            data-id="<?= $prov->IDproveedor ?>"
                                            data-rut="<?= html_escape($prov->rut) ?>"
                                            data-nombre="<?= html_escape($prov->nombre) ?>"
                                            data-email="<?= html_escape($prov->email) ?>"
                                            data-telefono="<?= html_escape($prov->telefono) ?>"
                                            data-estado="<?= $prov->IDestado ?>"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- MODAL: Agregar proveedor -->
        <div class="modal fade" id="modalAdd" tabindex="-1" aria-labelledby="modalAddLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="post" action="<?= site_url('AdminProveedores/agregar_proveedor') ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddLabel">Agregar proveedor</h5>
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
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" placeholder="Carlos Pérez" name="nombre" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" placeholder="+56995462835" name="telefono" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-check2 me-1"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: Editar proveedor -->
        <div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form class="modal-content" method="post" action="<?= site_url('AdminProveedores/actualizar_proveedor') ?>">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalEditLabel">Editar proveedor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="hidden" name="IDproveedor" id="editID">

                        <div class="row g-2">
                            <div class="col-12 col-md-6">
                                <label class="form-label">RUT</label>
                                <input type="text" class="form-control" id="editRut" name="rut" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">E-mail</label>
                                <input type="email" class="form-control" id="editEmail" name="e_mail" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editNombre" name="nombre" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="editTelefono" name="telefono" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label">Estado</label>
                                <select class="form-select" id="editEstado" name="IDestado">
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-check2 me-1"></i>Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de búsqueda y filtro -->
<script>
    const inputBusqueda = document.getElementById('busqueda');
    const filtroEstado = document.getElementById('filtroEstado');
    const filas = document.querySelectorAll('#tablaProveedores tbody tr');

    function aplicarFiltros() {
        const texto = inputBusqueda.value.toLowerCase();
        const estado = filtroEstado.value;

        filas.forEach(fila => {
            const coincideTexto = fila.textContent.toLowerCase().includes(texto);
            const estadoFila = fila.getAttribute('data-estado');
            const coincideEstado = (estado === 'todos') || (estado === estadoFila);
            fila.style.display = (coincideTexto && coincideEstado) ? '' : 'none';
        });
    }

    inputBusqueda.addEventListener('keyup', aplicarFiltros);
    filtroEstado.addEventListener('change', aplicarFiltros);

    document.querySelectorAll('.btn-editar').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('editID').value = btn.dataset.id;
            document.getElementById('editRut').value = btn.dataset.rut;
            document.getElementById('editNombre').value = btn.dataset.nombre;
            document.getElementById('editEmail').value = btn.dataset.email;
            document.getElementById('editTelefono').value = btn.dataset.telefono;
            document.getElementById('editEstado').value = btn.dataset.estado;
        });
    });
</script>

