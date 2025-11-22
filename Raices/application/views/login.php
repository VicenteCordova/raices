<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <title>Ingresar | Raíces</title>

        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap/bootstrap.min.css') ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        <style>
            /* ===== Fondo general con degradado ===== */
            body {
                background: linear-gradient(135deg, #111 0%, #222 40%, #3b2e00 100%);
                color: #f9f9f9;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            /* ===== Tarjeta del login ===== */
            .login-card {
                background: #1b1b1b;
                border: 1px solid #444;
                width: 100%;
                max-width: 420px;
                border-radius: 14px;
                box-shadow: 0 4px 25px rgba(255, 180, 0, 0.25);
                overflow: hidden;
            }

            .login-card .card-body {
                padding: 2rem;
            }

            /* ===== Título ===== */
            .login-title {
                font-weight: 600;
                text-align: center;
                margin-bottom: 1.5rem;
                color: #ffd95a;
                letter-spacing: 0.5px;
            }

            /* ===== Formularios ===== */
            .form-label {
                color: #e6e6e6;
            }

            .form-control {
                background: #2a2a2a;
                border: 1px solid #555;
                color: #f1f1f1;
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .form-control:focus {
                border-color: #ffcc33;
                box-shadow: 0 0 0 0.25rem rgba(255, 204, 51, 0.25);
                background: #2d2d2d;
                color: #fff;
            }

            /* ===== Checkbox y links ===== */
            .form-check-label {
                font-size: 0.9rem;
                color: #ddd;
            }

            a.small {
                text-decoration: none;
                color: #ffcc33;
                transition: color 0.2s ease;
            }

            a.small:hover {
                color: #ffb300;
            }

            /* ===== Botón ===== */
            .btn-primary {
                background: linear-gradient(90deg, #ffcc33 0%, #ffb300 100%);
                border: none;
                color: #222;
                font-weight: 600;
                letter-spacing: 0.3px;
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .btn-primary:hover {
                background: linear-gradient(90deg, #ffb300 0%, #ffcc33 100%);
                color: #111;
                transform: scale(1.02);
                box-shadow: 0 0 10px rgba(255, 204, 51, 0.5);
            }

            /* ===== Footer ===== */
            .login-footer {
                text-align: center;
                margin-top: 1.5rem;
                font-size: 0.9rem;
                color: #cfcfcf;
            }

            .login-footer strong {
                color: #ffd95a;
            }

            /* ===== Adaptación móvil ===== */
            @media (max-width: 576px) {
                .login-card {
                    max-width: 90%;
                }

            }

            input::placeholder {
                color: #cccccc; /* gris suave */
                opacity: 1;  /* asegura que no sea semitransparente */
            }

            ::-webkit-input-placeholder {
                color: #bbbbbb !important;
            }
            :-ms-input-placeholder {
                color: #bbbbbb !important;
            }
            ::-ms-input-placeholder {
                color: #bbbbbb !important;
            }
            :-moz-placeholder {
                color: #bbbbbb !important;
                opacity: 1;
            }
            ::-moz-placeholder {
                color: #bbbbbb !important;
                opacity: 1;
            }
        </style>
    </head>
    <body>

        <div class="login-card">
            <div class="card-body">

                <!-- Título -->
                <h4 class="login-title">Ingresar al sistema</h4>

                <!-- Mostrar error si existe -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= $error ?></div>
                <?php endif; ?>

                <!-- Mostrar error de validación via flashdata -->
                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-danger text-center"><?= $this->session->flashdata('error'); ?></div>
                <?php endif; ?>

                <!-- Formulario funcional -->
                <form method="post" action="<?= base_url('login/do_login') ?>" autocomplete="off">
                    <div class="mb-3">
                        <label for="usuario" class="form-label fw-semibold">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required autofocus placeholder="Ingresa tu usuario o correo">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Ingresa tu contraseña">
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Recordarme</label>
                        </div>
                        <a href="#" class="small">¿Olvidaste tu contraseña?</a>
                    </div>

                    <div class="d-grid mt-4">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Entrar
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="login-footer">
                    Acceso exclusivo para personal de <strong>Raíces</strong><br>
                    © <?= date('Y') ?> — Raices
                </div>

            </div>
        </div>

        <script src="<?= base_url('assets/js/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    </body>
</html>
