<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<style>
    :root { --alto-hf: 120px; } /* alto del header + footer */
    .contenido-scroll{ height: calc(100vh - var(--alto-hf)); overflow-y: auto; background: #f7f7f7; }

    /* ===== LAYOUT DE DOS COLUMNAS (izquierda apilada) ===== */
    .admin-shell{ height: calc(100vh - var(--alto-hf) - 2rem); /* 2rem ~ margenes top/bottom del container */ overflow: hidden; }
    .left-rail{
        display: flex; flex-direction: column; gap: 12px;
        height: 100%; min-height: 0;
    }
    .left-rail .panel{ display:flex; flex-direction:column; flex:1 1 0; min-height:0; }
    .panel .card-header{ background:#fff; border:0; padding:12px 12px 0 12px; }
    .panel .card-footer{ background:#fff; border:0; padding:8px 12px 12px 12px; }
    .panel .panel-body{ padding:8px 12px 12px 12px; overflow:auto; flex:1 1 0; min-height:0; }

    /* Detalle derecha ocupa todo el alto disponible */
    .detail-pane .card{ height:100%; }

    /* Ítems / imágenes */
    .thumb-mini { width:36px; height:36px; object-fit:cover; border-radius:8px; margin-right:8px; }
    .thumb-lg{ width:220px; height:220px; object-fit:cover; border-radius:14px; }
    .product-item.active-select { background:#ffe082 !important; }

    /* Badges y meta */
    .status-dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:6px; }
    .status-active   { background:#28a745; }
    .status-inactive { background:#dc3545; }
    .meta-badge { background:#f1f3f5; color:#444; }
</style>

<div class="contenido-scroll">
    <div class="container-sm my-4 admin-shell">

        <!-- Mensajes flash -->
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
        <?php endif; ?>

        <div class="row g-3 h-100">

            <!-- ================== COLUMNA IZQUIERDA: PRODUCTOS + CATEGORÍAS ================== -->
            <aside class="col-lg-4">
                <div class="left-rail">

                    <!-- ===== Productos ===== -->
                    <div class="card panel shadow-sm" id="panelProductos">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Productos <span class="badge bg-secondary ms-1" id="countProd"><?= isset($items) && is_array($items) ? count($items) : 0 ?></span></h6>
                            </div>

                            <!-- Filtro habilitados / deshabilitados -->
                            <div class="btn-group w-100 mt-2" role="group" aria-label="Filtros">
                                <input type="radio" class="btn-check" name="filterRadio" id="filterActivos" autocomplete="off" checked>
                                <label class="btn btn-outline-secondary" for="filterActivos"><i class="bi bi-check2-circle me-1"></i>Habilitados</label>
                                <input type="radio" class="btn-check" name="filterRadio" id="filterInactivos" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="filterInactivos"><i class="bi bi-x-circle me-1"></i>Deshabilitados</label>
                            </div>

                            <!-- Buscador de productos -->
                            <input type="search" id="qProductos" class="form-control form-control-sm mt-2" placeholder="Buscar producto por nombre o SKU…">
                        </div>

                        <div class="panel-body">
                            <div class="list-group list-group-flush" id="listaProductos">
                                <?php
                                if (!empty($items)): foreach ($items as $p):
                                        $img = base_url('assets/img/productos/' . $p->skuProducto . '.jpg');
                                        $placeholder = 'https://picsum.photos/seed/' . $p->skuProducto . '/80/80';
                                        $isActive = isset($producto) && $producto->skuProducto === $p->skuProducto;
                                        ?>
                                        <a href="<?= site_url('adminProductos/ver/' . $p->skuProducto) ?>"
                                           class="list-group-item list-group-item-action d-flex align-items-center <?= $isActive ? 'active' : '' ?> product-item"
                                           data-precio="<?= (float) $p->precio ?>"
                                           data-name="<?= strtolower($p->nombre . ' ' . $p->skuProducto) ?>">
                                            <img src="<?= $img ?>?t=<?= time() ?>" class="thumb-mini border" onerror="this.src='<?= $placeholder ?>';" alt="img">
                                            <span class="flex-fill">
                                                <?= html_escape($p->nombre) ?>
                                                <small class="text-muted d-block">SKU: <?= html_escape($p->skuProducto) ?></small>
                                            </span>
                                            <span class="badge bg-light text-dark"><?= '$' . number_format((float) $p->precio, 0, ',', '.') ?></span>
                                        </a>
                                        <?php
                                    endforeach;
                                else:
                                    ?>
                                    <div class="list-group-item"><small class="text-muted">Sin resultados</small></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
                                <i class="bi bi-plus-circle me-1"></i>Agregar producto
                            </button>
                        </div>
                    </div>

                    <!-- ===== Categorías (debajo) ===== -->
                    <div class="card panel shadow-sm" id="panelCategorias">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Categorías <span class="badge bg-secondary ms-1" id="countCat"><?= isset($categorias) && is_array($categorias) ? count($categorias) : 0 ?></span></h6>
                            </div>

                            <!-- Buscador de categorías -->
                            <input type="search" id="qCategorias" class="form-control form-control-sm mt-2" placeholder="Buscar categoría…">
                        </div>

                        <div class="panel-body">
                            <div class="list-group list-group-flush" id="listaCategorias">
                                <?php if (!empty($categorias)): foreach ($categorias as $c): ?>
                                        <div class="list-group-item d-flex align-items-center justify-content-between cat-item" data-name="<?= strtolower($c->nombre) ?>">
                                            <span class="me-2 text-truncate"><?= html_escape($c->nombre) ?></span>
                                            <a href="<?= site_url('adminProductos/categoria_delete/' . $c->IDcategoria) ?>"
                                               class="btn btn-sm btn-outline-danger btn-delete-cat"
                                               data-nombre="<?= html_escape($c->nombre) ?>">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                        <?php
                                    endforeach;
                                else:
                                    ?>
                                    <div class="list-group-item"><small class="text-muted">Sin categorías</small></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalAddCategory">
                                <i class="bi bi-folder-plus me-1"></i>Nueva categoría
                            </button>
                        </div>
                    </div>

                </div>
            </aside>

            <!-- ================== COLUMNA DERECHA: FICHA DE PRODUCTO ================== -->
            <main class="col-lg-8 detail-pane">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Ficha del producto</h5>
                        </div>

                        <?php
                        if (isset($producto)):
                            $imgBig = base_url('assets/img/productos/' . $producto->skuProducto . '.jpg');
                            $placeholderBig = 'https://picsum.photos/seed/' . $producto->skuProducto . '/400/400';
                            $habilitado = ((float) $producto->precio > 0);
                            ?>
                    <!--                            <form method="post" action="<?= site_url('adminProductos/producto_update/' . $producto->skuProducto) ?>">
                                                    <div class="row g-4">
                                                        <div class="col-lg-4 text-center">
                                                            <img src="<?= $imgBig ?>?t=<?= time() ?>" class="thumb-lg border"
                                                                 onerror="this.src='<?= $placeholderBig ?>';" alt="img">
                                                                                                <div class="mt-3 d-grid gap-2">
                                                                                                    <a href="<?= site_url('adminProductos/imagen/' . $producto->skuProducto) ?>" class="btn btn-outline-secondary btn-sm">
                                                                                                        <i class="bi bi-image"></i> Cambiar imagen
                                                                                                    </a>
                                                                                                </div>
                                                            <div class="mt-3 d-grid gap-2" id="imageUploadContainer" style="display:none;">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-8">
                                                            <div class="row g-3">
                                                                <div class="col-12">
                                                                    <label class="form-label">Nombre</label>
                                                                    <input type="text" name="nombre" class="form-control" required
                                                                           value="<?= html_escape($producto->nombre) ?>">
                                                                </div>

                                                                <div class="col-12 col-md-6">
                                                                    <label class="form-label">Categoría</label>
                                                                    <select class="form-select" name="IDcategoria" required>
                            <?php foreach ((array) $categorias as $c): ?>
                                                                                                <option value="<?= (int) $c->IDcategoria ?>" <?= $c->IDcategoria == $producto->IDcategoria ? 'selected' : '' ?>>
                                <?= html_escape($c->nombre) ?>
                                                                                                </option>
                            <?php endforeach; ?>
                                                                    </select>
                                                                </div>

                                                                <div class="col-12 col-md-3">
                                                                    <label class="form-label">Precio</label>
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">$</span>
                                                                        <input type="number" name="precio" min="0" step="1" class="form-control" required
                                                                               value="<?= (int) $producto->precio ?>">
                                                                    </div>
                                                                </div>

                                                                <div class="col-12 col-md-3">
                                                                    <label class="form-label">SKU</label>
                                                                    <input type="text" class="form-control" value="<?= html_escape($producto->skuProducto) ?>" disabled>
                                                                </div>
                                                            </div>

                                                            <hr class="my-3">
                                                                                                    <div class="d-flex flex-wrap gap-2">
                                                                                                        <button class="btn btn-success" type="submit"><i class="bi bi-save me-1"></i>Guardar cambios</button>
                                                            
                                                                                                        <a href="<?= site_url('adminProductos/producto_disable/' . $producto->skuProducto) ?>"
                                                                                                           class="btn btn-outline-danger btn-disable-prod">
                                                                                                            <i class="bi bi-slash-circle me-1"></i>Deshabilitar
                                                                                                        </a>
                                                                                                    </div>

                                                            <div class="mt-3 small">
                                                                <span class="badge meta-badge me-2">IDcategoría: <?= (int) $producto->IDcategoria ?></span>
                                                                <span class="badge meta-badge">Estado: <?= ((float) $producto->precio > 0) ? 'Habilitado' : 'Deshabilitado' ?></span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </form>-->
                            <form method="post"
                                  action="<?= site_url('adminProductos/producto_update/' . $producto->skuProducto) ?>"
                                  enctype="multipart/form-data"
                                  id="productoForm">

                                <div class="row g-4">
                                    <div class="col-lg-4 text-center">
                                        <?php
                                        $imgBig = base_url('assets/img/productos/' . $producto->skuProducto . '.jpg');
                                        $placeholderBig = 'https://picsum.photos/seed/' . $producto->skuProducto . '/400/400';
                                        ?>
                                        <img src="<?= $imgBig ?>?t=<?= time() ?>" class="thumb-lg border mb-2"
                                             onerror="this.src='<?= $placeholderBig ?>';" alt="img">

                                        <div id="imageUploadContainer" style="display:none;">
                                            <input type="file" name="imagen" accept="image/jpeg" class="form-control form-control-sm">
                                        </div>
                                    </div>

                                    <div class="col-lg-8">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label">Nombre</label>
                                                <input type="text" name="nombre" class="form-control" required
                                                       value="<?= html_escape($producto->nombre) ?>" disabled>
                                            </div>

                                            <div class="col-12 col-md-6">
                                                <label class="form-label">Categoría</label>
                                                <select class="form-select" name="IDcategoria" required disabled>
                                                    <?php foreach ((array) $categorias as $c): ?>
                                                        <option value="<?= (int) $c->IDcategoria ?>" <?= $c->IDcategoria == $producto->IDcategoria ? 'selected' : '' ?>>
                                                            <?= html_escape($c->nombre) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-12 col-md-3">
                                                <label class="form-label">Precio</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" name="precio" min="0" step="1" class="form-control" required
                                                           value="<?= (int) $producto->precio ?>" disabled>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-3">
                                                <label class="form-label">SKU</label>
                                                <input type="text" class="form-control" value="<?= html_escape($producto->skuProducto) ?>" disabled>
                                            </div>
                                        </div>

                                        <!--                                        <hr class="my-3">
                                        
                                                                                <div class="d-flex flex-wrap gap-2">
                                                                                    <button type="button" id="btnEditar" class="btn btn-primary">
                                                                                        <i class="bi bi-pencil-square me-1"></i> Editar
                                                                                    </button>
                                        
                                                                                    <button class="btn btn-success" type="submit" id="btnGuardar" style="display:none;">
                                                                                        <i class="bi bi-save me-1"></i> Guardar cambios
                                                                                    </button>
                                        
                                                                                    <a href="<?= site_url('adminProductos/producto_disable/' . $producto->skuProducto) ?>"
                                                                                       class="btn btn-outline-danger btn-disable-prod">
                                                                                        <i class="bi bi-slash-circle me-1"></i> Deshabilitar
                                                                                    </a>
                                                                                </div>-->

                                        <!--                                        <div class="mt-3 small">
                                                                                    <span class="badge meta-badge me-2">IDcategoría: <?= (int) $producto->IDcategoria ?></span>
                                                                                    <span class="badge meta-badge">Estado: <?= ((float) $producto->precio > 0) ? 'Habilitado' : 'Deshabilitado' ?></span>
                                                                                </div>-->
                                        <div class="col-12 mt-2">
                                            <label class="form-label d-block">Estado</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="switchHabilitado"
                                                       name="habilitado" <?= $habilitado ? 'checked' : '' ?> disabled>
                                                <label class="form-check-label" for="switchHabilitado">
                                                    <?= $habilitado ? 'Habilitado' : 'Deshabilitado' ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-box-seam" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">Selecciona un producto en la lista o agrega uno nuevo.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>

            <!-- ===== Offcanvas móvil (solo productos) ===== -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasProducts" aria-labelledby="offcanvasProductsLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasProductsLabel">Productos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="btn-group w-100 mb-3" role="group" aria-label="Filtros">
                        <input type="radio" class="btn-check" name="filterRadioMobile" id="filterActivosM" autocomplete="off" checked>
                        <label class="btn btn-outline-secondary" for="filterActivosM"><i class="bi bi-check2-circle me-1"></i>Habilitados</label>
                        <input type="radio" class="btn-check" name="filterRadioMobile" id="filterInactivosM" autocomplete="off">
                        <label class="btn btn-outline-secondary" for="filterInactivosM"><i class="bi bi-x-circle me-1"></i>Deshabilitados</label>
                    </div>

                    <div class="list-group">
                        <?php
                        if (!empty($items)): foreach ($items as $p):
                                $img = base_url('assets/img/productos/' . $p->skuProducto . '.jpg');
                                $placeholder = 'https://picsum.photos/seed/' . $p->skuProducto . '/80/80';
                                ?>
                                <a href="<?= site_url('adminProductos/ver/' . $p->skuProducto) ?>"
                                   class="list-group-item list-group-item-action d-flex align-items-center" data-precio="<?= (float) $p->precio ?>">
                                    <img src="<?= $img ?>?t=<?= time() ?>" class="thumb-mini border" onerror="this.src='<?= $placeholder ?>';" alt="img">
                                    <span class="ms-1 flex-fill"><?= html_escape($p->nombre) ?></span>
                                    <span class="badge bg-light text-dark"><?= (int) $p->precio ?></span>
                                </a>
                                <?php
                            endforeach;
                        else:
                            ?>
                            <div class="list-group-item"><small class="text-muted">Sin resultados</small></div>
                        <?php endif; ?>
                    </div>

                    <button class="btn btn-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
                        <i class="bi bi-plus-circle me-1"></i>Agregar producto
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ===== MODAL: Agregar producto ===== -->
<div class="modal fade" id="modalAddProduct" tabindex="-1" aria-labelledby="modalAddProductLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <?= form_open_multipart('adminProductos/store', ['class' => 'modal-content', 'id' => 'formAddProduct', 'autocomplete' => 'off']); ?>
        <div class="modal-header">
            <h5 class="modal-title" id="modalAddProductLabel">Agregar producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <label class="form-label">SKU</label>
                    <input type="text" name="skuProducto" id="skuProducto" class="form-control"
                           placeholder="Ej. ALM500" required pattern="[A-Za-z0-9\-_.]+"
                           title="Solo letras, números o - _ .">
                    <div class="form-text">Se usará también para el nombre de la imagen.</div>
                </div>

                <div class="col-12">
                    <label class="form-label">Nombre del producto</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej. Almendras 500g" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Categoría</label>
                    <select name="IDcategoria" class="form-select" required>
                        <?php foreach ((array) $categorias as $c): ?>
                            <option value="<?= (int) $c->IDcategoria ?>"><?= html_escape($c->nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label">Precio</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" name="precio" min="0" step="1" class="form-control" placeholder="6490" required>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label">Peso (g)</label>
                    <input type="number" name="peso" min="0" step="10" class="form-control" placeholder="500">
                </div>

                <div class="col-12">
                    <label class="form-label">Imagen (JPG máx. 2MB)</label>
                    <input type="file" name="imagen" id="imagen" class="form-control" accept=".jpg,.jpeg">
                    <div class="form-text">Se guardará como <strong>{SKU}.jpg</strong> en <code>assets/img/productos/</code>.</div>
                    <img id="previewAdd" class="mt-2 rounded border d-none" alt="Vista previa" style="max-height:140px;">
                </div>

                <div class="col-12">
                    <div class="form-check form-switch mt-1">
                        <input class="form-check-input" type="checkbox" name="habilitado" id="estadoNuevoProducto" checked>
                        <label class="form-check-label" for="estadoNuevoProducto">Habilitado</label>
                    </div>
                    <div class="form-text">Para deshabilitar: precio 0.</div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
            <button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i>Guardar</button>
        </div>
        <?= form_close(); ?>
    </div>
</div>

<!-- ===== MODAL: Agregar categoría ===== -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" aria-labelledby="modalAddCategoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" action="<?= site_url('adminProductos/categoria_store') ?>" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddCategoryLabel">Agregar categoría</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" required minlength="2" placeholder="Ej. Snacks">
                <div class="form-text">Debe ser único (uq_categoria_nombre).</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
                <button class="btn btn-primary" type="submit"><i class="bi bi-check2 me-1"></i>Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('form[action*="producto_update"]');
        if (!form)
            return;

        const imgContainer = document.getElementById('imageUploadContainer'); // div vacío en HTML
        const imgEl = form.closest('.row').querySelector('img.thumb-lg');

        // Crear barra de acciones (Guardar / Cancelar) y ocultarla
        const actionBar = document.createElement('div');
        actionBar.className = 'd-flex gap-2 mt-3 d-none';
        actionBar.innerHTML = `
    <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Guardar</button>
    <button type="button" id="btnCancelar" class="btn btn-outline-secondary">Cancelar</button>
  `;
        form.appendChild(actionBar);

        // Crear botón Editar y añadir al header
        const header = form.closest('.card-body').querySelector('.d-flex.justify-content-between');
        const btnEditar = document.createElement('button');
        btnEditar.type = 'button';
        btnEditar.className = 'btn btn-outline-primary btn-sm';
        btnEditar.innerHTML = `<i class="bi bi-pencil"></i> Editar`;
        header.appendChild(btnEditar);

        // Estado local
        let fileInput = null;
        let previewImg = null;

        // Aseguramos que el contenedor arranque vacío y oculto
        if (imgContainer) {
            imgContainer.innerHTML = ''; // nada dentro
            imgContainer.style.display = 'none';
        }

        btnEditar.addEventListener('click', () => {
            // habilitar inputs excepto SKU (si existiera)
            form.querySelectorAll('input, select').forEach(i => {
                if (i.hasAttribute('disabled'))
                    i.removeAttribute('disabled');
                if (i.hasAttribute('readonly'))
                    i.removeAttribute('readonly');
                if (i.name === 'skuProducto') {
                    i.setAttribute('readonly', 'readonly');
                    i.setAttribute('disabled', 'disabled');
                }
            });

            // insertar el input file solo si no existe ya
            if (imgContainer && !imgContainer.querySelector('input[type="file"]')) {
                // mostrar contenedor
                imgContainer.style.display = 'block';

                // crear input file
                fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'imagen';
                fileInput.accept = 'image/jpeg,image/jpg';
                fileInput.className = 'form-control mb-2';

                // crear preview img debajo
                previewImg = document.createElement('img');
                previewImg.alt = 'Preview';
                previewImg.className = 'mt-2 rounded border';
                previewImg.style.maxHeight = '140px';
                previewImg.style.display = 'none';

                imgContainer.appendChild(fileInput);
                imgContainer.appendChild(previewImg);

                // listener para vista previa y validación
                fileInput.addEventListener('change', (ev) => {
                    const f = fileInput.files && fileInput.files[0];
                    if (!f) {
                        previewImg.style.display = 'none';
                        previewImg.src = '';
                        return;
                    }
                    if (!/^image\/jpe?g$/i.test(f.type)) {
                        alert('Formato no permitido. Usa JPG/JPEG.');
                        fileInput.value = '';
                        previewImg.style.display = 'none';
                        return;
                    }
                    // tamaño opcional: 2MB
                    if (f.size > 2 * 1024 * 1024) {
                        alert('Imagen demasiado grande. Máx 2MB.');
                        fileInput.value = '';
                        previewImg.style.display = 'none';
                        return;
                    }
                    previewImg.src = URL.createObjectURL(f);
                    previewImg.style.display = 'block';
                });
            }

            btnEditar.classList.add('d-none');
            actionBar.classList.remove('d-none');
            // opcional: desplazar a la sección de edición
            if (imgContainer)
                imgContainer.scrollIntoView({behavior: 'smooth', block: 'center'});
        });

        // Cancelar: recargar para restaurar estado (simple)
        form.addEventListener('click', (e) => {
            if (e.target && e.target.id === 'btnCancelar') {
                e.preventDefault();
                window.location.reload();
            }
        });

        // Envío: si hay file lo incluye
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const fd = new FormData(form);
            if (fileInput && fileInput.files[0]) {
                fd.append('imagen', fileInput.files[0]);
            }
            const url = form.getAttribute('action');

            try {
                const resp = await fetch(url, {method: 'POST', body: fd});
                if (resp.ok) {
                    // si tu backend responde JSON con éxito, podrías comprobarlo; aquí recargamos:
                    window.location.reload();
                } else {
                    const text = await resp.text();
                    alert('Error al guardar los cambios. ' + (text || ''));
                }
            } catch (err) {
                alert('Error de red al guardar.');
                console.error(err);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {

        const btnEditar = document.getElementById('btnEditar');
        const btnGuardar = document.getElementById('btnGuardar');
        const inputs = document.querySelectorAll('#productoForm input, #productoForm select');
        const imageUpload = document.getElementById('imageUploadContainer');
        const switchHabilitado = document.getElementById('switchHabilitado');
        const labelSwitch = switchHabilitado.nextElementSibling;

        // Mostrar texto dinámico del switch
        switchHabilitado.addEventListener('change', () => {
            labelSwitch.textContent = switchHabilitado.checked ? 'Habilitado' : 'Deshabilitado';
        });

        // Al hacer clic en "Editar"
        btnEditar.addEventListener('click', () => {
            inputs.forEach(el => {
                if (el.name !== 'skuProducto')
                    el.removeAttribute('disabled');
            });
            imageUpload.style.display = 'block';
            btnEditar.style.display = 'none';
            btnGuardar.style.display = 'inline-block';
        });
    });
</script>


