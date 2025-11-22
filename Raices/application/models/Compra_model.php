<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Compra_model extends CI_Model
{
    private $t_compra  = 'compra';
    private $t_detalle = 'detalle_compra';

    public function all(){
        return $this->db->order_by('IDcompra','desc')->get($this->t_compra)->result();
    }

    public function find($id){
        return $this->db->get_where($this->t_compra, ['IDcompra'=>(int)$id])->row();
    }

    public function detalle($id){
        $this->db->select('d.*, p.nombre AS producto');
        $this->db->from($this->t_detalle.' d');
        $this->db->join('producto p','p.skuProducto=d.skuProducto','left');
        $this->db->where('d.IDcompra',(int)$id);
        return $this->db->order_by('d.IDdetalle_compra','asc')->get()->result();
    }

    public function create_header($data){
        $this->db->insert($this->t_compra,$data);
        return (int)$this->db->insert_id();
    }

    /**
     * Suma si existe misma (IDcompra, skuProducto). Requiere UNIQUE(IDcompra, skuProducto).
     * Es compatible con esquemas con o sin columna 'subtotal' en detalle_compra.
     */
    public function add_or_sum_detalle($IDcompra, $sku, $cantidad, $precio_unit){
        $IDcompra   = (int)$IDcompra;
        $sku        = strtoupper(trim($sku));
        $cantidad   = (float)$cantidad;
        $precioUnit = (float)$precio_unit;

        $hasSubtotal = $this->db->field_exists('subtotal', $this->t_detalle);
        $subtotal    = $cantidad * $precioUnit;

        // Intentar actualizar/sumar si la línea ya existe
        $this->db->set('cantidad_kg', "cantidad_kg+{$cantidad}", false)
                 ->set('costo_uni_kg', $precioUnit)
                 ->where('IDcompra', $IDcompra)
                 ->where('skuProducto', $sku);

        if ($hasSubtotal) {
            $this->db->set('subtotal', "subtotal+{$subtotal}", false);
        }

        $this->db->update($this->t_detalle);

        // Si no tocó filas, insertar
        if ($this->db->affected_rows() === 0) {
            $row = [
                'IDcompra'     => $IDcompra,
                'skuProducto'  => $sku,
                'cantidad_kg'  => $cantidad,
                'costo_uni_kg' => $precioUnit,
            ];
            if ($hasSubtotal) {
                $row['subtotal'] = $subtotal;
            }
            return $this->db->insert($this->t_detalle, $row);
        }
        return true;
    }

    /**
     * Actualiza el total en 'compra' usando la columna disponible:
     * - Si existe 'total' => actualiza 'total'
     * - Si existe 'precio_total' => actualiza 'precio_total'
     * - Si no existe ninguna, no falla (retorna true)
     * Calcula con SUM(subtotal) si existe 'subtotal', si no con SUM(cantidad_kg * costo_uni_kg)
     */
    public function update_total($IDcompra){
        $IDcompra = (int)$IDcompra;

        $hasSubtotal = $this->db->field_exists('subtotal', $this->t_detalle);

        if ($hasSubtotal) {
            $sum   = $this->db->select_sum('subtotal','monto')
                              ->get_where($this->t_detalle, ['IDcompra'=>$IDcompra])->row();
            $total = (float)($sum->monto ?? 0);
        } else {
            // SUM(cantidad_kg * costo_uni_kg)
            $sum   = $this->db->select('SUM(cantidad_kg * costo_uni_kg) AS monto', false)
                              ->get_where($this->t_detalle, ['IDcompra'=>$IDcompra])->row();
            $total = (float)($sum->monto ?? 0);
        }

        // Elegir la columna de total que exista
        if ($this->db->field_exists('total', $this->t_compra)) {
            $colTotal = 'total';
        } elseif ($this->db->field_exists('precio_total', $this->t_compra)) {
            $colTotal = 'precio_total';
        } else {
            // No existe columna para almacenar el total: no tratamos como error duro
            return true;
        }

        return $this->db->where('IDcompra',$IDcompra)
                        ->update($this->t_compra, [$colTotal => $total]);
    }

    /**
     * Guarda cabecera y detalles en una transacción y actualiza el total.
     * $header: ['IDproveedor'=>..., 'fecha'=>..., ...]
     * $detalles: [['sku'=>..., 'cantidad'=>..., 'precio_unit'=>...], ...]
     */
    public function save_with_details($header, array $detalles){
        $this->db->trans_start();

        $IDcompra = $this->create_header($header);

        foreach ($detalles as $it){
            $this->add_or_sum_detalle(
                $IDcompra,
                $it['sku'],
                $it['cantidad'],
                $it['precio_unit']
            );
        }

        $this->update_total($IDcompra);

        $this->db->trans_complete();
        return $this->db->trans_status() ? (int)$IDcompra : false;
    }
}
