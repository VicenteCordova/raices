<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="container my-4">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
    <?php endif; ?>

    <h3>Venta Nº <?= html_escape($venta->IDventa) ?></h3>
    <p><strong>Fecha:</strong> <?= html_escape($venta->hora) ?></p>
    <p><strong>Vendedor:</strong> <?= html_escape($venta->nombre_usuario ?? '—') ?></p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad (kg/unid)</th>
                <th>Precio unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $total = 0; foreach ($detalle as $d): 
                $sub = $d->kg_vendido * $d->precio_por_gramo;
                $total += $sub;
            ?>
            <tr>
                <td><?= html_escape($d->producto) ?></td>
                <td><?= number_format($d->kg_vendido, 2) ?></td>
                <td>$<?= number_format($d->precio_por_gramo, 0, ',', '.') ?></td>
                <td>$<?= number_format($sub, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h5 class="text-end mt-3">Total: $<?= number_format($total, 0, ',', '.') ?></h5>

    <div class="mt-4 text-center">
        <a href="<?= site_url('carrito') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver al carrito
        </a>
    </div>
</div>