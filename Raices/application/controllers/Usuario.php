<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {
  public function __construct(){
    parent::__construct();
    $this->load->helper(['url','form','auth']);
    require_role([1]); // solo admin
    $this->load->library(['form_validation','session']);
    $this->load->model(['Usuario_model'=>'usuarios']);
  }

  public function index(){
    $data['items'] = $this->usuarios->all();
    $this->load->view('usuarios/index',$data);
  }

  public function create(){ $this->load->view('usuarios/form'); }

  public function store(){
    $this->form_validation->set_rules('rut','RUT','required|min_length[8]');
    $this->form_validation->set_rules('e_mail','Email','required|valid_email');
    $this->form_validation->set_rules('password','Contraseña','required|min_length[6]');
    $this->form_validation->set_rules('IDestado','Estado','required|integer');
    $this->form_validation->set_rules('IDcargo','Cargo','required|integer');
    $this->form_validation->set_rules('IDrol','Rol','required|integer');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('usuarios/create');
    }
    $data = [
      'rut'       => trim($this->input->post('rut',true)),
      'nombre'    => trim($this->input->post('nombre',true)),
      'a_paterno' => trim($this->input->post('a_paterno',true)),
      'a_materno' => trim($this->input->post('a_materno',true)),
      'e_mail'    => trim($this->input->post('e_mail',true)),
      'password'  => password_hash($this->input->post('password',true), PASSWORD_BCRYPT),
      'IDestado'  => (int)$this->input->post('IDestado',true),
      'IDcargo'   => (int)$this->input->post('IDcargo',true),
      'IDrol'     => (int)$this->input->post('IDrol',true),
    ];
    if($this->usuarios->existsRut($data['rut'])){
      $this->session->set_flashdata('error','RUT ya registrado.');
      return redirect('usuarios/create');
    }
    if($this->usuarios->existsEmail($data['e_mail'])){
      $this->session->set_flashdata('error','Email ya registrado.');
      return redirect('usuarios/create');
    }
    $this->usuarios->create($data);
    $this->session->set_flashdata('ok','Usuario creado.');
    redirect('usuarios');
  }

  public function edit($IDusuario){
    $data['item'] = $this->usuarios->find((int)$IDusuario);
    if(!$data['item']) show_404();
    $this->load->view('usuarios/form',$data);
  }

  public function update($IDusuario){
    $this->form_validation->set_rules('e_mail','Email','required|valid_email');
    $this->form_validation->set_rules('IDestado','Estado','required|integer');
    $this->form_validation->set_rules('IDcargo','Cargo','required|integer');
    $this->form_validation->set_rules('IDrol','Rol','required|integer');
    if(!$this->form_validation->run()){
      $this->session->set_flashdata('error', validation_errors());
      return redirect('usuarios/edit/'.$IDusuario);
    }
    $data = [
      'nombre'    => trim($this->input->post('nombre',true)),
      'a_paterno' => trim($this->input->post('a_paterno',true)),
      'a_materno' => trim($this->input->post('a_materno',true)),
      'e_mail'    => trim($this->input->post('e_mail',true)),
      'IDestado'  => (int)$this->input->post('IDestado',true),
      'IDcargo'   => (int)$this->input->post('IDcargo',true),
      'IDrol'     => (int)$this->input->post('IDrol',true),
    ];
    if($this->usuarios->existsEmail($data['e_mail'], (int)$IDusuario)){
      $this->session->set_flashdata('error','Email ya registrado.');
      return redirect('usuarios/edit/'.$IDusuario);
    }
    if($this->input->post('password')){
      $data['password'] = password_hash($this->input->post('password',true), PASSWORD_BCRYPT);
    }
    $this->usuarios->update((int)$IDusuario, $data);
    $this->session->set_flashdata('ok','Usuario actualizado.');
    redirect('usuarios');
  }

  public function delete($IDusuario){
    $this->usuarios->delete((int)$IDusuario);
    $this->session->set_flashdata('ok','Usuario eliminado.');
    redirect('usuarios');
  }
}
