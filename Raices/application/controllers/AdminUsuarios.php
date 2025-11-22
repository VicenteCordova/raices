<?php defined('BASEPATH') OR exit('No direct script access allowed');

class AdminUsuarios extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['url','form']);
        $this->load->library(['session', 'form_validation']);
        // Carga de modelos con alias claros
        $this->load->model('Usuario_model', 'usuarios');
        $this->load->model('Cargo_model',   'cargos'); // si ya lo cargas en otro lado, omite esta línea
    }

    public function index()
    {
        $data = [
            // Ajusta al método que tengas: all(), listar(), obtenerUsuarios(), etc.
            'usuarios'           => $this->usuarios->list_with_cargo_estado(),
            'cargos'             => $this->cargos->all(),              // si no usas Cargo_model, pasa tu arreglo $cargos
            'cant_trabajadores'  => $this->usuarios->cantidad_trabajadores(),
        ];

        $this->load->view('plantilla/header', $data);
        $this->load->view('adminUsuarios/adminUsuarios', $data);
        $this->load->view('plantilla/footer');
    }


    /** POST desde el modal "Agregar trabajador" */
    public function agregar_usuario()
    {
        $this->form_validation->set_rules('rut','RUT','required|min_length[8]');
        $this->form_validation->set_rules('e_mail','E-mail','required|valid_email');
        $this->form_validation->set_rules('nombre','Nombres','required');
        $this->form_validation->set_rules('a_paterno','Apellido Paterno','required');
        $this->form_validation->set_rules('a_materno','Apellido Materno','required');
        $this->form_validation->set_rules('IDcargo','Cargo','required|integer');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('show_modal', true);
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            return redirect('AdminUsuarios');
        }

        $rut      = trim($this->input->post('rut', true));
        $email    = trim($this->input->post('e_mail', true));

        // Validaciones de unicidad
        if ($this->usuarios->existsRut($rut)) {
            $this->session->set_flashdata('show_modal', true);
            $this->session->set_flashdata('error','El RUT ya está registrado.');
            return redirect('AdminUsuarios');
        }
        if ($this->usuarios->existsEmail($email)) {
            $this->session->set_flashdata('show_modal', true);
            $this->session->set_flashdata('error','El E-mail ya está registrado.');
            return redirect('AdminUsuarios');
        }
        
        $password_hash = password_hash($rut, PASSWORD_BCRYPT);

        // Datos a guardar (ajusta defaults según tu BD)
        $data = [
            'rut'       => $rut,
            'e_mail'    => $email,
            'nombre'    => trim($this->input->post('nombre', true)),
            'a_paterno' => trim($this->input->post('a_paterno', true)),
            'a_materno' => trim($this->input->post('a_materno', true)),
            'IDcargo'   => (int)$this->input->post('IDcargo', true),
            'password'  => $password_hash,
            'IDestado'  => 1,       // 1 = Activo (mapeo con la vista)
            'IDrol'     => (int)$this->input->post('IDcargo', true) == 1 ? 1 : 2,       
        ];

        $this->usuarios->create($data);
        $this->session->set_flashdata('show_modal', false);
        $this->session->set_flashdata('success','Trabajador agregado correctamente.');
        return redirect('AdminUsuarios');
    }

    /**
     * POST por fetch desde el panel de edición.
     * Espera: id, nombre, a_paterno, a_materno, email, IDcargo, estado(on/1/2)
     * Responde JSON: {success:true} / {success:false, error:"..."}
     */
    public function editar_usuario()
    {
        $id = (int)$this->input->post('id', true);
        if ($id <= 0) { return $this->_json(false, 'ID inválido'); }

        $email   = trim($this->input->post('email', true));
        $IDcargo = (int)$this->input->post('IDcargo', true);
        $estadoP = $this->input->post('estado'); // 'on' | null | '1' | '2'

        $IDestado = ( ($estadoP === 'on') || ($estadoP === '1') || ($estadoP === 1) ) ? 1 : 2;

        // Validación básica
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->_json(false, 'E-mail inválido');
        }
        if ($IDcargo <= 0) {
            return $this->_json(false, 'Cargo inválido');
        }
        if ($this->usuarios->existsEmail($email, $id)) {
            return $this->_json(false, 'El E-mail ya está registrado en otro usuario.');
        }

        $data = [
            'nombre'    => trim($this->input->post('nombre', true)),
            'a_paterno' => trim($this->input->post('a_paterno', true)),
            'a_materno' => trim($this->input->post('a_materno', true)),
            'e_mail'    => $email,
            'IDcargo'   => $IDcargo,
            'IDestado'  => $IDestado,
        ];

        $ok = $this->usuarios->update($id, $data);
        if (!$ok) { return $this->_json(false, 'No se pudo actualizar.'); }

        return $this->_json(true);
    }

    private function _json($success, $error = '')
    {
        $this->output->set_content_type('application/json')
                     ->set_output(json_encode(['success' => (bool)$success, 'error' => $error]));
    }
}
