    </main>

    <!-- Footer dentro de la columna derecha -->
    <footer class="bg-dark text-white text-center fw-bold py-3 mt-auto w-100">
      <div class="container">
        © Sistema de Gestión <?= date('Y') ?> - Todos los derechos reservados
      </div>
    </footer>
  </div> <!-- cierra columna derecha -->

  <!-- Scripts -->
  <script src="<?= base_url('assets/js/bootstrap/bootstrap.bundle.min.js') ?>"></script>
  <script src="<?= base_url('assets/js/sweetalert2/sweetalert2.all.min.js') ?>"></script>

  <script>
    <?php if ($this->session->flashdata('success')): ?>
      Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: '<?= $this->session->flashdata('success'); ?>',
        confirmButtonColor: '#198754'
      });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?= $this->session->flashdata('error'); ?>',
        confirmButtonColor: '#d33'
      });
    <?php endif; ?>
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const botones = document.querySelectorAll('.btn-eliminar');
      botones.forEach(boton => {
        boton.addEventListener('click', function(e) {
          e.preventDefault();
          const url = this.getAttribute('href');
          Swal.fire({
            title: '¿Estás seguro?',
            text: '¡No podrás deshacer esto!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar'
          }).then((result) => {
            if (result.isConfirmed) { window.location.href = url; }
          });
        });
      });
    });
  </script>

</body>
</html>
