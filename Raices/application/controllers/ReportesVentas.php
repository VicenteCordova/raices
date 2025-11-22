<?php defined('BASEPATH') or exit('No direct script access allowed');

class ReportesVentas extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Si usas sesión/guard, hazlo aquí (no llames session_start manualmente).
        // $this->load->library('session');
        // if (!$this->session->userdata('usuario')) redirect('login');
    }

    // GET /reportes/ventas
    public function index()
    {
        $data['titulo'] = 'Reportes de Ventas -  Raices';
         $data['active'] = 'reporte';
        
        $this->load->view('plantilla/header', $data);     // navbar + <body>
        $this->load->view('reporte/reportesVentas');             // solo UI (sin datos)
        $this->load->view('plantilla/footer');            // footer + cierre </body>
    }
}
