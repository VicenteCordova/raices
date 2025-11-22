<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Reporte_model extends CI_Model {
  public function resumenDiario(){
    return $this->db->order_by('fecha','desc')->get('vw_resumen_ventas_por_dia')->result();
  }
  public function ventaDetalleExpandido($IDventa){
    return $this->db->get_where('vw_venta_detalle_expandida',['IDventa'=>(int)$IDventa])->result();
  }
}
