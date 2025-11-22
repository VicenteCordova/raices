<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Compras extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->helper(['url','form']);
    $this->load->library(['form_validation','session']);
    $this->load->model(['Compra_model' => 'compras', 'Proveedor_model' => 'proveedores']);
  }

  // POST desde la vista de Bodega (compra completa)
  public function store_full(){
    $this->form_validation->set_rules('IDproveedor','Proveedor','required|integer');
    $this->form_validation->set_rules('fecha','Fecha','required');

    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', strip_tags(validation_errors()));
      return redirect('bodega');
    }

    $IDproveedor = (int)$this->input->post('IDproveedor', true);
    if(!$this->proveedores->find($IDproveedor)){
      $this->session->set_flashdata('error','Proveedor inválido.');
      return redirect('bodega');
    }

    // Encabezado
    $header = [
      'IDproveedor' => $IDproveedor,
      'fecha'       => $this->input->post('fecha', true),
      // total se recalcula en el modelo
    ];

    // Detalle (arrays)
    $sku      = (array)$this->input->post('sku', true);
    $cant     = (array)$this->input->post('cantidad', true);
    $precio   = (array)$this->input->post('precio_unit', true);

    $detalles = [];
    for($i=0; $i<count($sku); $i++){
      $s = trim($sku[$i] ?? '');
      $c = (float)($cant[$i] ?? 0);
      $p = (float)($precio[$i] ?? 0);
      if($s !== '' && $c > 0){
        $detalles[] = ['sku'=>$s, 'cantidad'=>$c, 'precio_unit'=>$p];
      }
    }

    if(empty($detalles)){
      $this->session->set_flashdata('error','Debes agregar al menos 1 producto en el detalle.');
      return redirect('bodega');
    }

    $ID = $this->compras->save_with_details($header, $detalles);
    if(!$ID){
      $this->session->set_flashdata('error','No se pudo guardar la compra.');
    }else{
      $this->session->set_flashdata('ok','Compra guardada (ID '.$ID.') y stock de bodega actualizado.');
    }
    redirect('bodega');
  }
}
