<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bodega extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->helper(['url','form']);           // <- NECESARIO para form_open
        $this->load->library(['session']);
        $this->load->model([
            'Proveedor_model' => 'proveedores',
            'Producto_model'  => 'productos',
        ]);
    }

    public function index(){
        $data['title']         = 'Bodega — Raíces';
        $data['active']        = 'bodega';
        $data['proveedores']   = $this->proveedores->activos(); // (id, nombre)
        $data['productos']     = $this->productos->activos();   // (skuProducto, nombre)
        $data['gramaje_venta'] = 100;                       // solo informativo
        $data['items']         = [];                        // filas para transformar (si aplica)
        $data['gramajes']      = [50,80,100,200,250,500];

        $this->load->view('plantilla/header', $data);
        $this->load->view('bodega/bodega', $data);   // vista única
        $this->load->view('plantilla/footer');
    }
}
