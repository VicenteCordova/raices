<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Categoria_model extends CI_Model {
  private $table = 'categoria';
  private $pk    = 'IDcategoria';

  public function all(){ return $this->db->order_by('nombre','asc')->get($this->table)->result(); }
  public function find($id){ return $this->db->get_where($this->table, [$this->pk => (int)$id])->row(); }
  public function existsByNombre($nombre, $excludeId=null){
    $this->db->where('nombre',$nombre);
    if($excludeId){ $this->db->where($this->pk.' !=',(int)$excludeId); }
    return (bool)$this->db->get($this->table)->row();
  }
  public function create($data){ return $this->db->insert($this->table,$data); }
  public function update($id,$data){ return $this->db->where($this->pk,(int)$id)->update($this->table,$data); }
  public function delete($id){ return $this->db->delete($this->table, [$this->pk=>(int)$id]); }
}
