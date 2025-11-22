<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
  // Lee datos de sesión con fallback y salida segura
  $nombre  = html_escape($this->session->userdata('nombre')  ?? 'Usuario');
  $usuario = html_escape($this->session->userdata('usuario') ?? '');
  $rol     = html_escape($this->session->userdata('rol') ?? 'usuario');
  $cargo = html_escape($this->session->userdata('cargo') ?? 'vendedor');

  // Definir flag admin (para controlar qué mostrar en el menú)
  $esAdmin = (strtolower($rol) === 'admin');
?>

<div class="container-sm mt-4 contenido-principal">

  <div class="card shadow border-0" style="background-color:#f7f7f7;">
    <!-- Header -->
    <div class="card-header d-flex align-items-center justify-content-between"
         style="background-color:#151515; color:#ffc400;">
      <h4 class="mb-0">
        <i class="bi bi-boxes me-2"></i> Raíces — Panel Principal
      </h4>
      <span class="badge" style="background:#ffc400; color:#151515;">Inicio</span>
    </div>

    <div class="card-body">

      <!-- Saludo -->
      <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-emoji-smile me-2"></i>
        <div>Hola, <strong><?= $nombre; ?></strong> 👋 ¡bienvenido/a!</div>
      </div>

      <!-- Info sesión -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-md-4">
          <div class="p-3 border rounded bg-white">
            <small class="text-muted d-block">Usuario</small>
            <span class="badge bg-secondary"><?= $usuario; ?></span>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="p-3 border rounded bg-white">
            <small class="text-muted d-block">Cargo</small>
            <span class="badge text-uppercase" style="background:#28a745;"><?= $cargo; ?></span>
          </div>
        </div>
        <div class="col-12 col-md-4">
          <div class="p-3 border rounded bg-white">
            <small class="text-muted d-block">Accesos</small>
            <span class="badge bg-dark">Menú lateral</span>
          </div>
        </div>
      </div>

      <hr>

      <!-- Logo -->
      <div class="text-center mt-4">
        <img src="<?= base_url('assets/img/logo-raices.png'); ?>" alt="Logo Raíces"
             class="img-fluid" style="max-width:518px;">
      </div>

    </div>
  </div>

</div>
