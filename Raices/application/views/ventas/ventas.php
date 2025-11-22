<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<?php
$rolSesion = $this->session->userdata('rol');
$idRolSesion = $this->session->userdata('IDrol');
if (is_numeric($rolSesion)) {
    $idRol = (int) $rolSesion;
} elseif (is_numeric($idRolSesion)) {
    $idRol = (int) $idRolSesion;
} else {
    $idRol = null;
}
$rolStr = is_string($rolSesion) ? strtolower($rolSesion) : null;
if (!$rolStr) {
    $map = [1 => 'admin', 2 => 'vendedor', 3 => 'bodeguero'];
    $rolStr = isset($map[$idRol]) ? $map[$idRol] : 'usuario';
}
$isAdmin = ($rolStr === 'admin');
$isVendedor = ($rolStr === 'vendedor');
$isBodeguero = ($rolStr === 'bodeguero');
$canSell = ($isAdmin || $isVendedor);
?>

<style>
    .product-img{ height:140px; object-fit:contain; }
    .cat-active{ background:#ffe082!important; }
    .pager .btn{ min-width:36px; }
    .qty-badge{ min-width:38px; }
</style>

<div class="contenido-scroll">
    <div class="container-sm my-4">
        <div class="row g-3">

            <!-- CATEGORÍAS -->
            <aside class="col-md-2 d-none d-md-block">
                <div class="list-group" id="catList">
                    <button class="list-group-item list-group-item-action cat-active" data-cat="all">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Todos
                    </button>

                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $c): ?>
                            <button class="list-group-item list-group-item-action" data-cat="<?= (int) $c->IDcategoria ?>">
                                <?= html_escape($c->nombre) ?>
                            </button>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <button class="list-group-item list-group-item-action disabled">Sin categorías</button>
                    <?php endif; ?>
                </div>
            </aside>

            <!-- GRID -->
            <main class="col-md-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">

                        <!-- Encabezado (sin link al carrito) -->
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="mb-0">Catálogo de Ventas</h5>

                            <div class="d-flex align-items-center gap-2">
                                <?php if ($isBodeguero): ?>
                                    <span class="badge bg-secondary">Solo visualización (Bodega)</span>
                                <?php elseif ($canSell): ?>
                                    <span class="badge bg-success">Modo venta</span>
                                <?php endif; ?>

                                <input type="search" id="q" class="form-control form-control-sm" style="max-width:260px"
                                       placeholder="Buscar producto...">
                            </div>
                        </div>
                        <!-- /Encabezado -->

                        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4" id="gridProductos">
                            <?php if (!empty($items)): foreach ($items as $p): ?>
                                    <?php
                                    $img = base_url('assets/img/productos/' . $p->skuProducto . '.jpg');
                                    $placeholder = 'https://picsum.photos/seed/' . $p->skuProducto . '/400/240';
                                    $cat = isset($p->IDcategoria) ? (int) $p->IDcategoria : 0;
                                    ?>
                                    <div class="col prod" data-cat="<?= $cat ?>" data-name="<?= html_escape($p->nombre) ?>">
                                        <div class="card h-100 text-center">
                                            <img src="<?= $img ?>?t=<?= time() ?>" class="card-img-top p-3 product-img"
                                                 onerror="this.src='<?= $placeholder ?>';"
                                                 alt="<?= html_escape($p->nombre) ?>">

                                            <div class="card-body">
                                                <h6 class="card-title mb-1"><?= html_escape($p->nombre) ?></h6>
                                                <p class="small text-muted mb-3">$<?= number_format((float) $p->precio, 0, ',', '.') ?></p>

                                                <?php if ($canSell): ?>
                                                    <?= form_open('carrito/agregar', ['class' => 'add-to-cart']); ?>
                                                    <input type="hidden" name="skuProducto" value="<?= html_escape($p->skuProducto) ?>">
                                                    <input type="hidden" name="cantidad" class="in-cant" value="0">

                                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm step" data-step="-10">&laquo;</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm step" data-step="-1">&lsaquo;</button>
                                                        <span class="badge bg-light text-dark px-3 qty qty-badge">0</span>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm step" data-step="1">&rsaquo;</button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm step" data-step="10">&raquo;</button>
                                                    </div>

                                                    <button type="button" class="btn btn-primary btn-sm mt-3 btn-add">
                                                        <i class="bi bi-cart-plus"></i> Agregar al carrito
                                                    </button>
                                                    <?= form_close(); ?>
                                                <?php else: ?>
                                                    <div class="text-muted small"><i class="bi bi-eye"></i> Sin permisos para vender</div>
                                                <?php endif; ?>

                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach;
                            else:
                                ?>
                                <div class="col"><div class="alert alert-light">No hay productos.</div></div>
<?php endif; ?>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-between align-items-center mt-3 pager">
                            <div>Mostrando <span id="countShown">0</span> de <span id="countTotal">0</span></div>
                            <div class="btn-group">
                                <button class="btn btn-outline-secondary btn-sm" id="prevPg">&laquo;</button>
                                <span class="btn btn-outline-secondary btn-sm disabled" id="pgInfo">1/1</span>
                                <button class="btn btn-outline-secondary btn-sm" id="nextPg">&raquo;</button>
                            </div>
                        </div>

                        <!-- Botón VER CARRITO abajo del card -->
                        <?php if ($canSell): ?>
                            <?php
                            $cart = $this->session->userdata('cart');
                            $cartCount = 0;
                            if (is_array($cart)) {
                                foreach ($cart as $it) {
                                    $cartCount += (int) $it['cantidad'];
                                }
                            }
                            ?>
                            <div class="mt-3 d-flex justify-content-end">
                                <a href="<?= site_url('carrito') ?>" class="btn btn-outline-primary">
                                    <i class="bi bi-cart3"></i>
                                    <span class="ms-1">Ver carrito</span>
                                    <?php if ($cartCount > 0): ?>
                                        <span class="badge bg-primary ms-1"><?= (int) $cartCount ?></span>
    <?php endif; ?>
                                </a>
                            </div>
<?php endif; ?>

                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<script>

    (function () {
        const cards = Array.from(document.querySelectorAll('#gridProductos .prod'));
        const countTotalEl = document.getElementById('countTotal');
        const countShownEl = document.getElementById('countShown');
        const pgInfo = document.getElementById('pgInfo');
        const prevPg = document.getElementById('prevPg');
        const nextPg = document.getElementById('nextPg');
        const q = document.getElementById('q');
        const catList = document.getElementById('catList');
        let page = 1, perPage = 9, cat = 'all', term = '';

        function apply() {
            const filtered = cards.filter(c => {
                const matchCat = (cat === 'all') || (String(c.dataset.cat) === String(cat));
                const matchText = c.dataset.name.toLowerCase().includes(term.toLowerCase());
                return matchCat && matchText;
            });
            if (countTotalEl)
                countTotalEl.textContent = filtered.length;

            const pages = Math.max(1, Math.ceil(filtered.length / perPage));
            page = Math.min(page, pages);
            const start = (page - 1) * perPage;
            const end = start + perPage;

            cards.forEach(c => c.classList.add('d-none'));
            filtered.slice(start, end).forEach(c => c.classList.remove('d-none'));

            if (countShownEl)
                countShownEl.textContent = filtered.slice(start, end).length;
            if (pgInfo)
                pgInfo.textContent = `${page}/${pages}`;
            if (prevPg)
                prevPg.disabled = (page <= 1);
            if (nextPg)
                nextPg.disabled = (page >= pages);
        }

        if (prevPg)
            prevPg.addEventListener('click', () => {
                page = Math.max(1, page - 1);
                apply();
            });
        if (nextPg)
            nextPg.addEventListener('click', () => {
                page = page + 1;
                apply();
            });
        if (q)
            q.addEventListener('input', () => {
                term = q.value;
                page = 1;
                apply();
            });

        if (catList)
            catList.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-cat]');
                if (!btn)
                    return;
                cat = btn.dataset.cat;
                page = 1;
                catList.querySelectorAll('.list-group-item').forEach(b => b.classList.remove('cat-active'));
                btn.classList.add('cat-active');
                apply();
            });

        // SIEMPRE operar dentro del .prod correcto
        document.getElementById('gridProductos').addEventListener('click', (e) => {
            const stepBtn = e.target.closest('.step');
            if (stepBtn) {
                const prod = e.target.closest('.prod');
                if (!prod)
                    return;
                const qty = prod.querySelector('.qty');
                let v = parseInt(qty.textContent || '0', 10) || 0;
                v = Math.max(0, v + parseInt(stepBtn.dataset.step, 10));
                qty.textContent = v;
                return;
            }

            const addBtn = e.target.closest('.btn-add');
            if (addBtn) {
                const prod = e.target.closest('.prod');
                if (!prod)
                    return;
                const qtyEl = prod.querySelector('.qty');
                const form = prod.querySelector('form.add-to-cart');
                if (!form) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Formulario no encontrado',
                        text: 'No se encontró el formulario del producto.',
                        confirmButtonColor: '#ffcc00',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }
                const hidden = form.querySelector('.in-cant');
                const qty = parseInt(qtyEl.textContent || '0', 10) || 0;
                if (qty <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Cantidad inválida',
                        text: 'Seleccione una cantidad mayor a 0.',
                        confirmButtonColor: '#ffcc00',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }
                hidden.value = qty;
                form.submit();
            }
        });

        apply();
    })();


</script>
