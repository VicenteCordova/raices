<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Producto_model extends CI_Model {

    private $table = 'producto';
    private $pk = 'skuProducto';

    public function all() {
        // si necesitas la categoría unida, luego podemos añadir join; por ahora simple
        return $this->db->order_by('nombre', 'asc')->get($this->table)->result();
    }

    public function activos() {
        return $this->db
                        ->where('precio >', 0)
                        ->get($this->table)
                        ->result();
    }

    public function find($sku) {
        return $this->db->get_where($this->table, [$this->pk => $sku])->row();
    }

    public function existsBySku($sku) {
        return (bool) $this->db->get_where($this->table, [$this->pk => $sku])->row();
    }

    public function create($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($sku, $data) {
        return $this->db->where($this->pk, $sku)->update($this->table, $data);
    }

    public function delete($sku) {
        return $this->db->delete($this->table, [$this->pk => $sku]);
    }

}
