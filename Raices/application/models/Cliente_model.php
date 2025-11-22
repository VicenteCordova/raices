<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente_model extends CI_Model {

  public function obtenerTodos() {
    return $this->db->get('VC_JG_Clientes')->result();
  }

  public function obtenerDatosId($id) {
    return $this->db->get_where('VC_JG_Clientes', ['id' => $id])->row();
  }

  public function insertar($run, $nombres, $apellidos, $telefono, $direccion) {
    $sql = "CALL VC_JG_InsertarCliente(?, ?, ?, ?, ?)";
    return $this->db->query($sql, [$run, $nombres, $apellidos, $telefono, $direccion]);
  }

  public function actualizar($id, $run, $nombres, $apellidos, $telefono, $correo, $direccion) {
    $sql = "CALL VC_JG_ActualizarCliente(?, ?, ?, ?, ?, ?, ?)";
    return $this->db->query($sql, [$id, $run, $nombres, $apellidos, $telefono, $correo, $direccion]);
  }

  public function eliminar($id) {
    $sql = "CALL VC_JG_EliminarCliente(?)";
    return $this->db->query($sql, [$id]);
  }
}
