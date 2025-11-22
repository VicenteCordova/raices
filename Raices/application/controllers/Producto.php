<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Productos extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->helper(['url','form','auth']);
    require_role([1,2,3]); // admin, vendedor, bodega
    $this->load->library(['form_validation','session']);
    $this->load->model(['Producto_model'=>'productos','Categoria_model'=>'categorias']);
  }

  public function index(){
    $data['items'] = $this->productos->all();
    $this->load->view('productos/index',$data);
  }

  public function create(){
    $data['categorias'] = $this->categorias->all();
    $this->load->view('productos/form',$data);
  }

  public function store(){
    $this->form_validation->set_rules('skuProducto','SKU','required|min_length[2]');
    $this->form_validation->set_rules('nombre','Nombre','required|min_length[2]');
    $this->form_validation->set_rules('precio','Precio por gramo','required|numeric|greater_than_equal_to[0]');
    $this->form_validation->set_rules('IDcategoria','Categoría','required|integer');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('productos/create');
    }
    $data = [
      'skuProducto' => trim($this->input->post('skuProducto',true)),
      'nombre'      => trim($this->input->post('nombre',true)),
      'precio'      => $this->input->post('precio',true),
      'IDcategoria' => (int) $this->input->post('IDcategoria',true),
    ];
    if($this->productos->existsBySku($data['skuProducto'])){
      $this->session->set_flashdata('error','Ya existe un producto con ese SKU.');
      return redirect('productos/create');
    }
    // Validar FK categoría
    if(!$this->categorias->find($data['IDcategoria'])){
      $this->session->set_flashdata('error','Categoría inválida.');
      return redirect('productos/create');
    }
    $this->productos->create($data);
    $this->session->set_flashdata('ok','Producto creado.');
    redirect('productos');
  }

  public function edit($sku){
    $data['item'] = $this->productos->find($sku);
    if(!$data['item']) show_404();
    $data['categorias'] = $this->categorias->all();
    $this->load->view('productos/form',$data);
  }

  public function update($sku){
    $this->form_validation->set_rules('nombre','Nombre','required|min_length[2]');
    $this->form_validation->set_rules('precio','Precio por gramo','required|numeric|greater_than_equal_to[0]');
    $this->form_validation->set_rules('IDcategoria','Categoría','required|integer');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('productos/edit/'.$sku);
    }
    $data = [
      'nombre'      => trim($this->input->post('nombre',true)),
      'precio'      => $this->input->post('precio',true),
      'IDcategoria' => (int) $this->input->post('IDcategoria',true),
    ];
    if(!$this->categorias->find($data['IDcategoria'])){
      $this->session->set_flashdata('error','Categoría inválida.');
      return redirect('productos/edit/'.$sku);
    }
    $this->productos->update($sku, $data);
    $this->session->set_flashdata('ok','Producto actualizado.');
    redirect('productos');
  }

  public function delete($sku){
    $this->productos->delete($sku);
    $this->session->set_flashdata('ok','Producto eliminado.');
    redirect('productos');
  }
}
