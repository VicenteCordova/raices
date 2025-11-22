<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->helper(['url','auth']);
    require_role([1,2,3]); // admin, vendedor, bodega
    $this->load->model('Reporte_model','reportes');
  }

  public function resumen(){
    $data['rows'] = $this->reportes->resumenDiario();
    $this->load->view('reportes/resumen',$data);
  }

  public function venta_detalle($IDventa){
    $data['rows'] = $this->reportes->ventaDetalleExpandido((int)$IDventa);
    $this->load->view('reportes/detalle',$data);
  }
}