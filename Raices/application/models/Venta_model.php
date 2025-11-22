<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Venta_model extends CI_Model {

    private $t_venta = 'ventas';
    private $t_detalle = 'detalle_venta';

    public function all() {
        return $this->db->order_by('IDventa', 'desc')->get($this->t_venta)->result();
    }

    public function find($id) {
        $this->db->select('v.*, 
        CONCAT(u.nombre, " ", u.a_paterno, " ", u.a_materno) AS nombre_usuario');
        $this->db->from($this->t_venta . ' v');
        $this->db->join('usuario u', 'u.IDusuario = v.IDusuario', 'left');
        $this->db->where('v.IDventa', (int) $id);
        return $this->db->get()->row();
    }

    public function detalle($id) {
        $this->db->select('d.*, p.nombre AS producto');
        $this->db->from($this->t_detalle . ' d');
        $this->db->join('producto p', 'p.skuProducto=d.skuProducto', 'left');
        $this->db->where('d.IDventa', (int) $id);
        return $this->db->order_by('d.IDdetalle_venta', 'asc')->get()->result();
    }

    public function create_header($data) {
        $this->db->insert($this->t_venta, $data);
        return (int) $this->db->insert_id();
    }

    public function add_detalle($data) {
        // UNIQUE (IDventa, skuProducto)
        return $this->db->insert($this->t_detalle, $data);
    }

}
