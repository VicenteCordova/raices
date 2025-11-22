<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container my-4">
  <h5 class="mb-3">Cambiar imagen — <?= html_escape($p->nombre) ?> (SKU: <?= html_escape($p->skuProducto) ?>)</h5>

  <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger"><?= $this->session->flashdata('error'); ?></div>
  <?php endif; ?>
  <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success"><?= $this->session->flashdata('success'); ?></div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-md-4">
      <?php
        $img = base_url('assets/img/productos/'.$p->skuProducto.'.jpg');
        $placeholder = 'https://picsum.photos/seed/'.$p->skuProducto.'/400/240';
      ?>
      <img id="preview" src="<?= $img ?>?t=<?= time() ?>" class="img-fluid rounded border"
           onerror="this.src='<?= $placeholder ?>';" alt="previsualización">
    </div>
    <div class="col-md-8">
      <?= form_open_multipart('adminProductos/subir_imagen/'.$p->skuProducto); ?>
        <div class="mb-3">
          <label class="form-label">Nueva imagen (JPG/PNG/WEBP, máx. 2MB)</label>
          <input type="file" name="imagen" id="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp" required>
        </div>
        <button class="btn btn-primary" type="submit"><i class="bi bi-upload"></i> Subir</button>
        <a href="<?= site_url('adminProductos') ?>" class="btn btn-secondary">Volver</a>
      <?= form_close(); ?>
    </div>
  </div>
</div>

<script>
document.getElementById('imagen').addEventListener('change', function(){
  const f = this.files[0]; if(!f) return;
  const url = URL.createObjectURL(f);
  document.getElementById('preview').src = url;
});
</script>
