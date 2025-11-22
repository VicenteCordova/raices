<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->helper(['url','form','auth']);
    require_role([1,2,3,4]); // todos autenticados
    $this->load->library(['form_validation','session']);
    $this->load->model('Categoria_model','categorias');
  }

  public function index(){
    $data['items'] = $this->categorias->all();
    $this->load->view('categorias/index',$data);
  }

  public function create(){ $this->load->view('categorias/form'); }

  public function store(){
    $this->form_validation->set_rules('nombre','Nombre','required|min_length[2]');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('categorias/create');
    }
    $nombre = trim($this->input->post('nombre',true));
    if($this->categorias->existsByNombre($nombre)){
      $this->session->set_flashdata('error','Ya existe una categoría con ese nombre.');
      return redirect('categorias/create');
    }
    $this->categorias->create(['nombre'=>$nombre]);
    $this->session->set_flashdata('ok','Categoría creada.');
    redirect('categorias');
  }

  public function edit($IDcategoria){
    $data['item'] = $this->categorias->find((int)$IDcategoria);
    if(!$data['item']) show_404();
    $this->load->view('categorias/form',$data);
  }

  public function update($IDcategoria){
    $this->form_validation->set_rules('nombre','Nombre','required|min_length[2]');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('categorias/edit/'.$IDcategoria);
    }
    $nombre = trim($this->input->post('nombre',true));
    if($this->categorias->existsByNombre($nombre, (int)$IDcategoria)){
      $this->session->set_flashdata('error','Ya existe una categoría con ese nombre.');
      return redirect('categorias/edit/'.$IDcategoria);
    }
    $this->categorias->update((int)$IDcategoria, ['nombre'=>$nombre]);
    $this->session->set_flashdata('ok','Categoría actualizada.');
    redirect('categorias');
  }

  public function delete($IDcategoria){
    $this->categorias->delete((int)$IDcategoria);
    $this->session->set_flashdata('ok','Categoría eliminada.');
    redirect('categorias');
  }
}
