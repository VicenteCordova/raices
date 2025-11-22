<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdminProveedores extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library(['session', 'form_validation']);
        // Carga de modelos con alias claros
//        $this->load->model('Usuario_model', 'usuarios');
//        $this->load->model('Cargo_model',   'cargos');
        $this->load->model('Proveedor_model', 'proveedores'); // si ya lo cargas en otro lado, omite esta línea
    }

    public function index() {
        $data = [
            // Ajusta al método que tengas: all(), listar(), obtenerUsuarios(), etc.
//            'usuarios'           => $this->usuarios->list_with_cargo_estado(),
//            'cargos'             => $this->cargos->all(),              // si no usas Cargo_model, pasa tu arreglo $cargos
            'proveedores' => $this->proveedores->all(),
        ];

        $this->load->view('plantilla/header', $data);
        $this->load->view('adminProveedores/adminProveedores', $data);
        $this->load->view('plantilla/footer');
    }

    /** POST desde el modal "Agregar proveedor" */
    public function agregar_proveedor() {
        $this->form_validation->set_rules('rut', 'RUT', 'required|min_length[8]');
        $this->form_validation->set_rules('e_mail', 'E-mail', 'required|valid_email');
        $this->form_validation->set_rules('nombre', 'Nombres', 'required');
        $this->form_validation->set_rules('telefono', 'required');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('show_modal', true);
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            return redirect('AdminProveedores');
        }

        $rut = trim($this->input->post('rut', true));
        $email = trim($this->input->post('e_mail', true));

        // Validaciones de unicidad
        if ($this->proveedores->existsRut($rut)) {
            $this->session->set_flashdata('show_modal', true);
            $this->session->set_flashdata('error', 'El RUT ya está registrado.');
            return redirect('AdminProveedores');
        }
        if ($this->proveedores->existsEmail($email)) {
            $this->session->set_flashdata('show_modal', true);
            $this->session->set_flashdata('error', 'El E-mail ya está registrado.');
            return redirect('AdminProveedores');
        }

        // Datos a guardar (ajusta defaults según tu BD)
        $data = [
            'rut' => $rut,
            'email' => $email,
            'nombre' => trim($this->input->post('nombre', true)),
            'telefono' => trim($this->input->post('telefono', true)),
            'IDestado' => 1
        ];

        $this->proveedores->create($data);
        $this->session->set_flashdata('show_modal', false);
        $this->session->set_flashdata('success', 'Proveedor agregado correctamente.');
        return redirect('AdminProveedores');
    }

    public function actualizar_proveedor() {
        $id = $this->input->post('IDproveedor');
        $data = [
            'rut' => $this->input->post('rut'),
            'nombre' => $this->input->post('nombre'),
            'email' => $this->input->post('e_mail'),
            'telefono' => $this->input->post('telefono'),
            'IDestado' => $this->input->post('IDestado'),
        ];

        $this->proveedores->update($id, $data);
        $this->session->set_flashdata('success', 'Proveedor actualizado correctamente.');
        redirect('AdminProveedores');
    }

}
