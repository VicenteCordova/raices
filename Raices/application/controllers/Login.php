<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model', 'usuarios');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
    }

    public function index() {
        if ($this->session->userdata('IDusuario') || $this->session->userdata('usuario')) {
            return redirect('inicio', 'refresh');
        }
        $this->load->view('login');
    }

    // 🔁 alias para compatibilidad si alguna vista usa /login/validar
    public function validar() {
        return $this->do_login();
    }

    public function do_login() {
        // Aceptar 'e_mail' o 'usuario' desde el form
        $login = $this->input->post('e_mail', true);
        if (!$login) {
            $login = $this->input->post('usuario', true);
        }

        // Reglas mínimas según contenido
        if ($login && strpos($login, '@') !== false) {
            $this->form_validation->set_rules('e_mail', 'Correo', 'required|valid_email',
                    ['required' => 'Ingrese correo', 'valid_email' => 'Correo inválido']);
        } else {
            $this->form_validation->set_rules('usuario', 'Usuario', 'required',
                    ['required' => 'Ingrese usuario o correo']);
        }
        $this->form_validation->set_rules('password', 'Contraseña', 'required',
                ['required' => 'Ingrese contraseña']);

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors());
            return redirect('login');
        }

        $pass = $this->input->post('password', true);

        // Buscar por correo o por RUT/usuario
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $u = $this->usuarios->findByEmail($login);
        } else {
            $u = $this->usuarios->findByRut($login);
            if (!$u) {
                $u = $this->usuarios->findByEmail($login);
            }
        }

        if (!$u || (int) $u->IDestado !== 1 || !password_verify($pass, $u->password)) {
            $this->session->set_flashdata('error', 'Credenciales inválidas o usuario inactivo.');
            return redirect('login');
        }

        $cargos = [
            1 => 'Administrador',
            2 => 'Vendedor',
            3 => 'Bodega'
        ];

        // Obtener nombre del cargo según IDcargo
        $cargoNombre = isset($cargos[(int) $u->IDcargo]) ? $cargos[(int) $u->IDcargo] : '';

        $rolNombre = ((int) $u->IDrol === 1) ? 'admin' : 'usuario';
        // 🔐 Sesión con claves "oficiales" y "antiguas" para compatibilidad
        $this->session->set_userdata([
            'IDusuario' => (int) $u->IDusuario,
            'IDrol' => (int) $u->IDrol,
            'nombre' => $u->nombre,
            // compat vistas/controladores que esperan 'usuario'/'rol'
            'usuario' => $login, // correo o rut según lo ingresado
            'rol' => $rolNombre,
            'cargo' => $u->cargo
        ]);

        return redirect('inicio', 'refresh');   // destino existente
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

}
