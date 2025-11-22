<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Raices</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap/bootstrap.min.css') ?>">
  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="<?= base_url('assets/css/sweetalert2/sweetalert2.min.css') ?>">

  <style>
    html, body { height: 100%; }
    .sidebar { min-height: 100vh; background-color: #111111; }
    
    .nav-link.active-link {
    color: #ffcc00 !important;
    transition: background-color 0.2s ease-in-out;
    font-weight: bold;
  }
  
/*    .nav-link.active-link {
    background-color: #ffcc00;  amarillo 
    color: #111 !important;      texto oscuro 
    font-weight: bold;
    border-radius: 4px;
  }*/
  
  .nav-link:hover { /* amarillo más pálido */
    color: #ffcc00 !important;
    transition: background-color 0.2s ease-in-out;
  }
  </style>
</head>

<body class="d-flex">

  <!-- Sidebar -->
  <nav class="sidebar p-3 d-flex flex-column justify-content-between">
    <div>
      <h4 class="text-white mb-2">Raíces</h4>

      <?php
      // --- Detección de rol compatible (numérico o string) ---
      $rolSesion   = $this->session->userdata('rol');   // 'admin', 'usuario'
      $cargoSesion = $this->session->userdata('cargo');  //Administrador, Vendedor, Bodega
      $cargoStr = is_string($cargoSesion) ? strtolower($cargoSesion) : null;
      
      $isAdmin     = ($rolSesion === 'admin');
      $isVendedor  = ($cargoSesion === 'Vendedor') || $isAdmin;
      $isBodeguero = ($cargoSesion === 'Bodega') || $isAdmin;

      $logoutUrl = site_url('login/logout'); // alias de salida compatible
      
      $current = $this->uri->segment(1); // obtiene el primer segmento de la URL (ej: 'inicio', 'ventas', etc.)

      ?>

      <p class="text-white mb-4">
        <?= html_escape($this->session->userdata('nombre')) ?>
        (<?= ucfirst($cargoStr) ?>)
      </p>

      <ul class="nav flex-column">
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'inicio') ? 'active-link' : '' ?>" href="<?= site_url('inicio') ?>">Inicio</a>
        </li>

        <?php if ($isVendedor): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'ventas') ? 'active-link' : '' ?>" href="<?= site_url('ventas') ?>">Ventas</a>
        </li>
        <?php endif; ?>

        <?php if ($isBodeguero): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'bodega') ? 'active-link' : '' ?>" href="<?= site_url('bodega') ?>">Bodega</a>
        </li>
        <?php endif; ?>

        <?php if ($isVendedor): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'reportesVentas') ? 'active-link' : '' ?>" href="<?= site_url('reportesVentas') ?>">Reporte de Ventas</a>
        </li>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'reportesAdmin') ? 'active-link' : '' ?>" href="<?= site_url('reportesAdmin') ?>">Reporte Administrador</a>
        </li>
        <?php endif; ?>

        <?php if ($isVendedor): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'carrito') ? 'active-link' : '' ?>" href="<?= site_url('carrito') ?>">Carrito de venta</a>
        </li>
        <?php endif; ?>

        <?php if ($isAdmin): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'adminUsuarios') ? 'active-link' : '' ?>" href="<?= site_url('adminUsuarios') ?>">Administración Usuarios</a>
        </li>
        <?php endif; ?>
        
        <?php if ($isAdmin): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'adminProveedores') ? 'active-link' : '' ?>" href="<?= site_url('adminProveedores') ?>">Administración Proveedores</a>
        </li>
        <?php endif; ?>

        <?php if ($isBodeguero): ?>
        <li class="nav-item mb-2">
          <a class="nav-link text-white <?= ($current == 'adminProductos') ? 'active-link' : '' ?>" href="<?= site_url('adminProductos') ?>">Administración Productos</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>

    <div>
      <a class="nav-link text-white" href="<?= $logoutUrl ?>">Cerrar Sesión</a>
    </div>
  </nav>

  <!-- Columna derecha: Contenido + Footer -->
  <div class="d-flex flex-column flex-grow-1 min-vh-100 w-100">
    <main class="flex-fill p-4">
      <!-- ... tu contenido ... -->
