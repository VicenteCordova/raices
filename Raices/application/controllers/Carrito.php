<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Carrito extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session']);
        $this->load->helper(['url', 'form']);
        $this->load->model('Producto_model', 'productos');
        $this->load->model('Venta_model', 'ventas');
    }

    /** Vista del carrito */
    public function index() {
        $cart = $this->session->userdata('cart');
        if (!is_array($cart)) {
            $cart = [];
        }

        // Mantener las claves SKU para evitar duplicados
        $data['items'] = $cart;

        $this->load->view('plantilla/header', $data);
        $this->load->view('carrito/carrito', $data);
        $this->load->view('plantilla/footer');
    }

    /** Solo admin (1) o vendedor (2) pueden operar ventas */
    private function canSell(): bool {
        $rolSesion = $this->session->userdata('rol');
        $idRolSesion = $this->session->userdata('IDrol');
        $idRol = null;
        if (is_numeric($rolSesion))
            $idRol = (int) $rolSesion;
        elseif (is_numeric($idRolSesion))
            $idRol = (int) $idRolSesion;
        return in_array($idRol, [1, 2], true);
    }

    /** Agregar ítem desde Ventas (suma si existe el mismo SKU) */
    public function agregar() {
        if (!$this->canSell()) {
            $this->session->set_flashdata('error', 'No tiene permisos para vender.');
            return redirect('ventas');
        }

        $sku = trim((string) $this->input->post('skuProducto', true));
        $cant = (int) $this->input->post('cantidad', true);

        if ($sku === '' || $cant <= 0) {
            $this->session->set_flashdata('error', 'SKU inválido o cantidad debe ser mayor a 0.');
            return redirect('ventas');
        }

        $p = $this->productos->find($sku);
        if (!$p) {
            $this->session->set_flashdata('error', 'Producto no encontrado.');
            return redirect('ventas');
        }

        $cart = $this->session->userdata('cart');
        if (!is_array($cart)) {
            $cart = [];
        }

        // Si ya existe, suma la cantidad
        if (isset($cart[$sku])) {
            $cart[$sku]['cantidad'] += $cant;
        } else {
            $cart[$sku] = [
                'sku' => $sku,
                'nombre' => $p->nombre,
                'precio' => (float) $p->precio,
                'cantidad' => $cant,
            ];
        }

        $this->session->set_userdata('cart', $cart);
        $this->session->set_flashdata('success', "Se agregaron {$cant} × {$p->nombre} al carrito.");
        return redirect('ventas');
    }

    /** Actualizar cantidades desde la tabla (0 = eliminar) */
    public function actualizar() {
//        $skus = (array)$this->input->post('sku', true);
//        $cans = (array)$this->input->post('cantidad', true);
//
//        $cart = $this->session->userdata('cart');
//        if (!is_array($cart)) { $cart = []; }
//
//        foreach ($skus as $i => $skuRaw) {
//            $sku  = trim((string)$skuRaw);
//            $cant = isset($cans[$i]) ? (int)$cans[$i] : 0;
//
//            if ($sku === '') continue;
//
//            if ($cant <= 0) {
//                unset($cart[$sku]); // eliminar si cantidad es 0
//            } else {
//                // actualizar cantidad
//                if (isset($cart[$sku])) {
//                    $cart[$sku]['cantidad'] = $cant;
//                } else {
//                    // producto agregado manualmente? lo buscamos en BD
//                    $p = $this->productos->find($sku);
//                    if ($p) {
//                        $cart[$sku] = [
//                            'sku'      => $sku,
//                            'nombre'   => $p->nombre,
//                            'precio'   => (float)$p->precio,
//                            'cantidad' => $cant,
//                        ];
//                    }
//                }
//            }
//        }
//
//        $this->session->set_userdata('cart', $cart);
//        $this->session->set_flashdata('success', 'Carrito actualizado.');
//        return redirect('carrito');
        $cants = (array) $this->input->post('cantidad', true); // ahora es ['SKU1'=>2, 'SKU2'=>5, ...]
        $cart = $this->session->userdata('cart') ?? [];

        foreach ($cants as $sku => $cant) {
            $sku = trim((string) $sku);
            $cant = (int) $cant;

            if ($cant <= 0) {
                unset($cart[$sku]);
            } elseif (isset($cart[$sku])) {
                $cart[$sku]['cantidad'] = $cant;
            }
        }

        $this->session->set_userdata('cart', $cart);
        $this->session->set_flashdata('success', 'Carrito actualizado.');
        redirect('carrito');
    }

    /** Quitar un SKU */
    public function quitar($sku = '') {
        $sku = trim((string) $sku);
        $cart = $this->session->userdata('cart');
        if (is_array($cart) && isset($cart[$sku])) {
            unset($cart[$sku]);
            $this->session->set_userdata('cart', $cart);
            $this->session->set_flashdata('success', 'Producto quitado del carrito.');
        }
        return redirect('carrito');
    }

    /** Vaciar carrito */
    public function vaciar() {
        $this->session->unset_userdata('cart');
        $this->session->set_flashdata('success', 'Carrito vaciado.');
        return redirect('carrito');
    }

    /** Confirmar venta → crea cabecera y detalle respetando triggers */
    public function confirmar() {
        if (!$this->canSell()) {
            $this->session->set_flashdata('error', 'No tiene permisos para confirmar ventas.');
            return redirect('carrito');
        }

        $cart = $this->session->userdata('cart');
        if (!is_array($cart) || empty($cart)) {
            $this->session->set_flashdata('error', 'El carrito está vacío.');
            return redirect('carrito');
        }

        $IDusuario = (int) $this->session->userdata('IDusuario');
        if ($IDusuario <= 0) {
            $this->session->set_flashdata('error', 'Sesión inválida.');
            return redirect('carrito');
        }

        $this->db->trans_begin();
        try {
            // Cabecera
            $IDventa = $this->ventas->create_header([
                'hora' => date('Y-m-d H:i:s'),
                'IDusuario' => $IDusuario,
            ]);

            // Detalle
            foreach ($cart as $sku => $it) {
                $p = $this->productos->find($sku);
                if (!$p)
                    continue;

                $cantidad = (int) $it['cantidad'];
                if ($cantidad <= 0)
                    continue;

                $peso = isset($p->peso) ? (int) $p->peso : 0;
                $precio = (float) $p->precio;

                $kg_vendido = ($peso > 0) ? ($cantidad * $peso / 1000.0) : $cantidad;
                $precio_por_gramo = ($peso > 0) ? ($precio / $peso) : $precio;

                $this->ventas->add_detalle([
                    'IDventa' => (int) $IDventa,
                    'skuProducto' => $sku,
                    'kg_vendido' => $kg_vendido,
                    'precio_por_gramo' => $precio_por_gramo,
                ]);
            }

            if ($this->db->trans_status() === false) {
                throw new Exception('Error de BD al confirmar venta.');
            }

            $this->db->trans_commit();

            $this->session->unset_userdata('cart');
            $this->session->set_flashdata('success', 'Venta confirmada correctamente.');
            return redirect('ventas/detalle/' . $IDventa);
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Confirmar venta falló: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'No se pudo confirmar la venta.');
            return redirect('carrito');
        }
    }

}
