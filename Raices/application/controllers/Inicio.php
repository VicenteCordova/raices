<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends CI_Controller {
  public function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->helper(['url']);
    // Acepta cualquiera de las dos claves (compat hacia atrás)
    if (!$this->session->userdata('IDusuario') && !$this->session->userdata('usuario')) {
      redirect('login');
    }
  }

  public function index() {
    $data = [
      'usuario_id'     => $this->session->userdata('IDusuario') ?: $this->session->userdata('usuario_id'),
      'usuario_nombre' => $this->session->userdata('nombre'),
      'usuario_login'  => $this->session->userdata('usuario'), // correo o rut
      'usuario_rol'    => $this->session->userdata('rol') ?: $this->session->userdata('IDrol')
    ];
    $this->load->view('plantilla/header', $data);
    $this->load->view('inicio', $data);
    $this->load->view('plantilla/footer', $data);
  }
}
