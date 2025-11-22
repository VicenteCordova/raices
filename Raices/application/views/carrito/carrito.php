<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<style>
    /* ===== Scroll solo en el contenido entre header y footer ===== */
    :root { --alto-hf: 120px; } /* ajusta si tu header+footer ocupan más/menos */

    .contenido-scroll{
        height: calc(100vh - var(--alto-hf));
        overflow-y: auto;
        background: #f7f7f7;
    }

    /* ===== Estilos propios de carrito ===== */
    .table thead th { white-space: nowrap; }
    .qty-input { width: 90px; }
    .product-col { min-width: 240px; }
    .summary-card { max-width: 420px; }
</style>

<div class="contenido-scroll">
    <main class="container-sm my-4">

        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
        <?php endif; ?>

        <!-- Encabezado -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 class="mb-0">Resumen de venta</h4>
            <small class="text-muted">Edita las cantidades antes de confirmar.</small>
        </div>

        <?php
        // items llega desde el controlador (array en sesión)
        $items = isset($items) ? $items : [];
        // Totales (si no vienen precalculados, los recalculamos aquí)
        $subtotal = 0;
        foreach ($items as $sku => $it) {
            $precio = (float) $it['precio'];
            $cantidad = (int) $it['cantidad'];
            $sub = $precio * $cantidad;

            $items[$sku]['precio'] = $precio;
            $items[$sku]['cantidad'] = $cantidad;
            $items[$sku]['subtotal'] = $sub;

            $subtotal += $sub;
        }
        $taxRate = 0; // % de impuestos si corresponde (por ahora 0)
        $impuestos = round($subtotal * ($taxRate / 100));
        $total = $subtotal + $impuestos;
        ?>

        <div class="row g-4">
            <!-- Tabla de productos -->
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <?php if (empty($items)): ?>
                            <!-- Estado vacío -->
                            <div class="text-center py-5">
                                <i class="bi bi-basket2" style="font-size:2rem;"></i>
                                <p class="mt-2 mb-0">Tu carrito está vacío.</p>
                                <small class="text-muted d-block mb-3">Vuelve al catálogo para agregar productos.</small>
                                <a href="<?= site_url('ventas') ?>" class="btn btn-primary">
                                    <i class="bi bi-shop"></i> Ir al catálogo
                                </a>
                            </div>
                        <?php else: ?>

                            <?= form_open('carrito/actualizar'); ?>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th class="product-col">Producto</th>
                                            <th class="text-end">Precio unit.</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-end">Subtotal</th>
                                            <th class="text-center">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $it): ?>
                                            <tr>
                                                <td>
                                                    <strong><?= html_escape($it['nombre']) ?></strong>
                                                    <div class="small text-muted">SKU: <?= html_escape($it['sku']) ?></div>
                                                </td>
                                                <td class="text-end">
                                                    $<?= number_format($it['precio'], 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" class="form-control form-control-sm qty-input"
                                                           min="0" name="cantidad[<?= html_escape($it['sku']) ?>]" value="<?= (int) $it['cantidad'] ?>">
                                                </td>
                                                <td class="text-end">
                                                    $<?= number_format($it['subtotal'], 0, ',', '.') ?>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?= site_url('carrito/quitar/' . $it['sku']) ?>"
                                                       class="btn btn-outline-danger btn-sm" title="Quitar">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <a href="<?= site_url('ventas') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-shop me-1"></i>Seguir vendiendo
                                </a>
                                <a href="<?= site_url('carrito/vaciar') ?>" class="btn btn-outline-danger">
                                    <i class="bi bi-x-circle me-1"></i>Vaciar
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i>Actualizar cantidades
                                </button>
                            </div>
                            <?= form_close(); ?>

                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Resumen y acciones -->
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm summary-card ms-lg-auto">
                    <div class="card-body">
                        <h5 class="card-title">Totales</h5>

                        <div class="d-flex justify-content-between small text-muted">
                            <span>Subtotal</span><span>$<?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Impuestos (<?= (int) $taxRate ?>%)</span><span>$<?= number_format($impuestos, 0, ',', '.') ?></span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between fs-5">
                            <strong>Total</strong><strong>$<?= number_format($total, 0, ',', '.') ?></strong>
                        </div>

                        <!-- === Bloque reemplazado tal como solicitaste === -->
                        <div class="d-grid gap-2 mt-4">
                            <?php if (!empty($items)): ?>
                                <a class="btn btn-outline-secondary" href="<?= site_url('carrito/vaciar') ?>">
                                    <i class="bi bi-x-circle me-1"></i>Cancelar venta
                                </a>
                                <a class="btn btn-success" href="<?= site_url('carrito/confirmar') ?>">
                                    <i class="bi bi-check2-circle me-1"></i>Confirmar venta
                                </a>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary" disabled>
                                    <i class="bi bi-x-circle me-1"></i>Cancelar venta
                                </button>
                                <button class="btn btn-success" disabled>
                                    <i class="bi bi-check2-circle me-1"></i>Confirmar venta
                                </button>
                            <?php endif; ?>
                        </div>
                        <!-- === /Bloque reemplazado === -->

                        <div class="form-text mt-3">
                            Puedes editar la <strong>cantidad</strong> directamente en la tabla.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>
