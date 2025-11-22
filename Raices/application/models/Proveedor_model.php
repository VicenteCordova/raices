<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor_model extends CI_Model {
  private $table = 'proveedor';
  private $pk    = 'IDproveedor';

  public function all(){ return $this->db->order_by('nombre','asc')->get($this->table)->result(); }
  
  public function activos() {
        return $this->db
                        ->where('IDestado =', 1)
                        ->get($this->table)
                        ->result();
    }
  
  public function find($id){ return $this->db->get_where($this->table, [$this->pk=>(int)$id])->row(); }
  public function existsRut($rut,$excludeId=null){
    $this->db->where('rut',$rut);
    if($excludeId){ $this->db->where($this->pk.' !=',(int)$excludeId); }
    return (bool)$this->db->get($this->table)->row();
  }
  public function existsEmail($email,$excludeId=null){
    $this->db->where('email',$email);
    if($excludeId){ $this->db->where($this->pk.' !=',(int)$excludeId); }
    return (bool)$this->db->get($this->table)->row();
  }
  public function create($data){ return $this->db->insert($this->table,$data); }
  public function update($id,$data){ return $this->db->where($this->pk,(int)$id)->update($this->table,$data); }
  public function delete($id){ return $this->db->delete($this->table, [$this->pk=>(int)$id]); }
}

