<?php defined('BASEPATH') or exit('No direct script access allowed');

class ReportesAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Aquí podrías validar sesión/rol si lo necesitas.
        // $this->load->library('session');
        // if ($this->session->userdata('rol') !== 'admin') redirect('login');
    }

    // GET /reportes-admin  (o /reportesadmin)
    public function index()
    {
        $data['titulo'] = 'Reportes — Administración';
        $this->load->view('plantilla/header', $data);
        $this->load->view('reporte/reportesAdmin'); // solo UI
        $this->load->view('plantilla/footer');
    }
}
