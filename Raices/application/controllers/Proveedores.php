<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->helper(['url','form','auth']);
    require_role([1,3]); // admin + bodega
    $this->load->library(['form_validation','session']);
    $this->load->model('Proveedor_model','proveedores');
  }

  public function index(){
    $data['items'] = $this->proveedores->all();
    $this->load->view('proveedores/index',$data);
  }

  public function create(){ $this->load->view('proveedores/form'); }

  private function rut_pattern_ok($rut){ return (bool) preg_match('/^[0-9kK\.\-]+$/',$rut); }

  public function store(){
    $this->form_validation->set_rules('rut','RUT','required|min_length[8]');
    $this->form_validation->set_rules('nombre','Nombre','required|min_length[2]');
    $this->form_validation->set_rules('email','Email','required|valid_email');
    $this->form_validation->set_rules('telefono','Teléfono','required|min_length[6]');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('proveedores/create');
    }
    $data = [
      'rut'      => trim($this->input->post('rut',true)),
      'nombre'   => trim($this->input->post('nombre',true)),
      'email'    => trim($this->input->post('email',true)),
      'telefono' => trim($this->input->post('telefono',true)),
    ];
    if(!$this->rut_pattern_ok($data['rut'])){
      $this->session->set_flashdata('error','Formato de RUT inválido.');
      return redirect('proveedores/create');
    }
    if($this->proveedores->existsRut($data['rut'])){
      $this->session->set_flashdata('error','RUT ya registrado.');
      return redirect('proveedores/create');
    }
    if($this->proveedores->existsEmail($data['email'])){
      $this->session->set_flashdata('error','Email ya registrado.');
      return redirect('proveedores/create');
    }
    $this->proveedores->create($data);
    $this->session->set_flashdata('ok','Proveedor creado.');
    redirect('proveedores');
  }

  public function edit($IDproveedor){
    $data['item'] = $this->proveedores->find((int)$IDproveedor);
    if(!$data['item']) show_404();
    $this->load->view('proveedores/form',$data);
  }

  public function update($IDproveedor){
    $this->form_validation->set_rules('rut','RUT','required|min_length[8]');
    $this->form_validation->set_rules('nombre','Nombre','required|min_length[2]');
    $this->form_validation->set_rules('email','Email','required|valid_email');
    $this->form_validation->set_rules('telefono','Teléfono','required|min_length[6]');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('proveedores/edit/'.$IDproveedor);
    }
    $data = [
      'rut'      => trim($this->input->post('rut',true)),
      'nombre'   => trim($this->input->post('nombre',true)),
      'email'    => trim($this->input->post('email',true)),
      'telefono' => trim($this->input->post('telefono',true)),
    ];
    if(!$this->rut_pattern_ok($data['rut'])){
      $this->session->set_flashdata('error','Formato de RUT inválido.');
      return redirect('proveedores/edit/'.$IDproveedor);
    }
    if($this->proveedores->existsRut($data['rut'], (int)$IDproveedor)){
      $this->session->set_flashdata('error','RUT ya registrado.');
      return redirect('proveedores/edit/'.$IDproveedor);
    }
    if($this->proveedores->existsEmail($data['email'], (int)$IDproveedor)){
      $this->session->set_flashdata('error','Email ya registrado.');
      return redirect('proveedores/edit/'.$IDproveedor);
    }
    $this->proveedores->update((int)$IDproveedor, $data);
    $this->session->set_flashdata('ok','Proveedor actualizado.');
    redirect('proveedores');
  }

  public function delete($IDproveedor){
    $this->proveedores->delete((int)$IDproveedor);
    $this->session->set_flashdata('ok','Proveedor eliminado.');
    redirect('proveedores');
  }
}
