<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ventas extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->helper(['url','form','auth']);
    $this->load->library(['form_validation','session']);
    // Modelos necesarios para el catálogo
    $this->load->model(['Producto_model' => 'productos', 'Categoria_model' => 'categoriasM', 'Venta_model' => 'ventas']);
  }

  public function index(){
    // Productos a mostrar en el catálogo
    $data['items'] = $this->productos->activos();
    // Categorías para el sidebar (tiempo real desde BD)
    $data['categorias'] = $this->categoriasM->all();

    $this->load->view('plantilla/header', $data);
    // Asegúrate de que el path y nombre coinciden con tu repo
    $this->load->view('ventas/ventas', $data);
    $this->load->view('plantilla/footer');
  }

  public function create(){
    $data['productos'] = $this->productos->all();
    $this->load->view('plantilla/header', $data);
    $this->load->view('ventas/create', $data);
    $this->load->view('plantilla/footer');
  }

  public function store_header(){
    $data = [
      'hora'      => date('Y-m-d H:i:s'),
      'IDusuario' => (int)$this->session->userdata('IDusuario'),
    ];
    $IDventa = $this->ventas->create_header($data);
    $this->session->set_flashdata('ok','Venta creada. Agregue detalle.');
    redirect('ventas/detalle/'.$IDventa);
  }

  public function detalle($IDventa){
    $venta = $this->ventas->find($IDventa);
    if(!$venta) show_404();

    $data['venta']   = $venta;
    $data['detalle'] = $this->ventas->detalle($IDventa); // ← singular, no 'detalles'
    $data['productos'] = $this->productos->all();

    $this->load->view('plantilla/header', $data);
    $this->load->view('ventas/detalle', $data);
    $this->load->view('plantilla/footer');
}

  public function add_detalle($IDventa){
    $this->form_validation->set_rules('skuProducto','Producto','required');
    $this->form_validation->set_rules('kg_vendido','Kg vendidos','required|numeric|greater_than[0]');
    $this->form_validation->set_rules('precio_por_gramo','Precio por gramo','required|numeric|greater_than_equal_to[0]');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('ventas/detalle/'.$IDventa);
    }
    $data = [
      'IDventa'          => (int)$IDventa,
      'skuProducto'      => trim($this->input->post('skuProducto',true)),
      'kg_vendido'       => $this->input->post('kg_vendido',true),
      'precio_por_gramo' => $this->input->post('precio_por_gramo',true)
    ];
    if(!$this->productos->find($data['skuProducto'])){
      $this->session->set_flashdata('error','Producto inválido.');
      return redirect('ventas/detalle/'.$IDventa);
    }
    $ok = $this->ventas->add_detalle($data);
    $this->session->set_flashdata($ok ? 'ok' : 'error',
      $ok ? 'Detalle agregado. Total actualizado por trigger.' :
            'Línea duplicada para el mismo producto en esta venta.');
    redirect('ventas/detalle/'.$IDventa);
  }
}
