<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cargo_model extends CI_Model
{
    private $table = 'cargo';

    public function all()
    {
        return $this->db->order_by('nombre','asc')->get($this->table)->result();
    }
}
