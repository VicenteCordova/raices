<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model
{
    /** Ajusta nombres si tu esquema difiere */
    private $table = 'usuario';
    private $pk    = 'IDusuario';

    /** Listado simple (orden alfabético) */
    public function all()
    {
        return $this->db
            ->order_by('nombre', 'asc')
            ->get($this->table)
            ->result();
    }

    /**
     * Listado con nombre de cargo y alias de estado
     * @param null|bool $soloActivos true=solo activos, false=solo inactivos, null=todos
     */
    public function list_with_cargo_estado($soloActivos = null)
    {
        $this->db->select('u.*, c.nombre AS cargo, u.IDestado AS estado');
        $this->db->from($this->table . ' u');
        $this->db->join('cargo c', 'c.IDcargo = u.IDcargo', 'left');

        if ($soloActivos === true) {
            $this->db->where('u.IDestado', 1);
        } elseif ($soloActivos === false) {
            $this->db->where('u.IDestado <>', 1);
        }

        $this->db->order_by('u.nombre', 'asc');
        return $this->db->get()->result();
    }

    /**
     * Cantidad de trabajadores
     * @param null|bool $soloActivos true=solo activos, false=solo inactivos, null=total
     */
    public function cantidad_trabajadores($soloActivos = null)
    {
        $this->db->from($this->table);
        if ($soloActivos === true) {
            $this->db->where('IDestado', 1);
        } elseif ($soloActivos === false) {
            $this->db->where('IDestado <>', 1);
        }
        return (int)$this->db->count_all_results();
    }

    /** Buscar por PK */
    public function find($id)
    {
        return $this->db
            ->get_where($this->table, [$this->pk => (int)$id], 1)
            ->row();
    }

    /** Buscar por RUT (normalizado) */
    public function findByRut($rut)
    {
        $rut = strtoupper(trim((string)$rut));
    if ($rut === '') return null;

    $this->db->select('u.*, c.nombre AS cargo');
    $this->db->from('usuario u');
    $this->db->join('cargo c', 'c.IDcargo = u.IDcargo', 'left');
    $this->db->where('u.rut', $rut);
    return $this->db->get()->row();
//        $rut = strtoupper(trim((string)$rut));
//        if ($rut === '') return null;
// 
//        return $this->db
//            ->get_where($this->table, ['rut' => $rut], 1)
//            ->row();
    }

    /** Buscar por email (normalizado) */
    public function findByEmail($email)
    {
        $email = strtolower(trim((string)$email));
    if ($email === '') return null;

    $this->db->select('u.*, c.nombre AS cargo');
    $this->db->from('usuario u');
    $this->db->join('cargo c', 'c.IDcargo = u.IDcargo', 'left');
    $this->db->where('u.e_mail', $email);
    return $this->db->get()->row();
    }

    /** Exists por RUT (opcional excluir ID) */
    public function existsRut($rut, $excludeId = null)
    {
        $rut = strtoupper(trim((string)$rut));
        if ($rut === '') return false;

        $this->db->where('rut', $rut);
        if ($excludeId) {
            $this->db->where($this->pk.' <>', (int)$excludeId);
        }
        return (bool) $this->db->limit(1)->get($this->table)->row();
    }

    /** Exists por email (opcional excluir ID) */
    public function existsEmail($email, $excludeId = null)
    {
        $email = strtolower(trim((string)$email));
        if ($email === '') return false;

        $this->db->where('e_mail', $email);
        if ($excludeId) {
            $this->db->where($this->pk.' <>', (int)$excludeId);
        }
        return (bool) $this->db->limit(1)->get($this->table)->row();
    }

    /** Crear */
    public function create(array $data)
    {
        // Normalizaciones útiles
        if (isset($data['rut']))    $data['rut']    = strtoupper(trim($data['rut']));
        if (isset($data['e_mail'])) $data['e_mail'] = strtolower(trim($data['e_mail']));
        return $this->db->insert($this->table, $data);
    }

    /** Actualizar */
    public function update($id, array $data)
    {
        if (isset($data['rut']))    $data['rut']    = strtoupper(trim($data['rut']));
        if (isset($data['e_mail'])) $data['e_mail'] = strtolower(trim($data['e_mail']));

        return $this->db
            ->where($this->pk, (int)$id)
            ->update($this->table, $data);
    }

    /** Eliminar */
    public function delete($id)
    {
        return $this->db->delete($this->table, [$this->pk => (int)$id]);
    }

    /** (Opcional) Cambiar estado rápidamente */
    public function set_estado($id, $activo = true)
    {
        return $this->db
            ->where($this->pk, (int)$id)
            ->update($this->table, ['IDestado' => $activo ? 1 : 2]);
    }
}
