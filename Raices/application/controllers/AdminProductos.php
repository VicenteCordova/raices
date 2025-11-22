<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdminProductos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['session', 'form_validation', 'upload']);
        $this->load->helper(['url', 'form']);
        $this->load->model('Producto_model', 'productos');

        // Solo admin (rol 1)
        if ((int) ($this->session->userdata('IDrol') ?: $this->session->userdata('rol')) !== 1) {
            redirect('inicio');
        }
    }

    public function index() {
        $this->load->model('Categoria_model', 'categorias_m');
        $data['categorias'] = $this->categorias_m->all();
        $data['items'] = $this->productos->all();

        $this->load->view('plantilla/header', $data);
        $this->load->view('adminProductos/adminProductos', $data);
        $this->load->view('plantilla/footer');
    }

    public function ver($sku) {
        $this->load->model('Categoria_model', 'categorias_m');
        $p = $this->productos->find($sku);
        if (!$p)
            show_404();

        $data['producto'] = $p;
        $data['categorias'] = $this->categorias_m->all();
        $data['items'] = $this->productos->all();

        $this->load->view('plantilla/header', $data);
        $this->load->view('adminProductos/adminProductos', $data);
        $this->load->view('plantilla/footer');
    }

    public function producto_update($sku) {
    $p = $this->productos->find($sku);
    if (!$p)
        show_404();

    $this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|min_length[2]');
    $this->form_validation->set_rules('IDcategoria', 'Categoría', 'required|integer');
    $this->form_validation->set_rules('precio', 'Precio', 'required|numeric|greater_than_equal_to[0]');

    if (!$this->form_validation->run()) {
        $this->session->set_flashdata('error', strip_tags(validation_errors()));
        return redirect('adminProductos/ver/' . $sku);
    }

    // Verificar switch habilitado
    $habilitado = $this->input->post('habilitado') ? true : false;
    $precio = $habilitado ? $this->input->post('precio', true) : 0;

    // Imagen
    $imagen = $p->imagen ?? null;
    if (!empty($_FILES['imagen']['name'])) {
        $config = [
            'upload_path' => './assets/img/productos/',
            'allowed_types' => 'jpg|jpeg|png',
            'max_size' => 2048,
            'file_name' => $sku . '.jpg',
            'overwrite' => true
        ];
        $this->upload->initialize($config);

        if ($this->upload->do_upload('imagen')) {
            $imagen = $sku . '.jpg';
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            return redirect('adminProductos/ver/' . $sku);
        }
    }

    $data = [
        'nombre' => $this->input->post('nombre', true),
        'IDcategoria' => (int) $this->input->post('IDcategoria', true),
        'precio' => $precio,
        'imagen' => $imagen
    ];

    if (!$this->productos->update($sku, $data)) {
        $this->session->set_flashdata('error', 'No se pudo actualizar el producto.');
    } else {
        $msg = $habilitado
            ? 'Producto habilitado y actualizado correctamente.'
            : 'Producto deshabilitado (precio = 0).';
        $this->session->set_flashdata('success', $msg);
    }

    return redirect('adminProductos/ver/' . $sku);
}

    /** Deshabilitar producto (criterio no intrusivo: precio = 0) */
    public function producto_disable($sku) {
        $p = $this->productos->find($sku);
        if (!$p)
            show_404();

        if (!$this->productos->update($sku, ['precio' => 0])) {
            $this->session->set_flashdata('error', 'No se pudo deshabilitar el producto.');
        } else {
            $this->session->set_flashdata('success', 'Producto deshabilitado (precio = 0). Puede reactivarlo asignando un precio > 0.');
        }
        return redirect('adminProductos/ver/' . $sku);
    }

    /** Crear categoría (ya la tienes; se deja aquí por completitud) */
    public function categoria_store() {
        $this->load->model('Categoria_model', 'categorias_m');
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|min_length[2]');
        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            return redirect('adminProductos');
        }
        $nombre = trim($this->input->post('nombre', true));
        if ($this->categorias_m->existsByNombre($nombre)) {
            $this->session->set_flashdata('error', 'La categoría ya existe.');
            return redirect('adminProductos');
        }
        $ok = $this->categorias_m->create(['nombre' => $nombre]);
        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Categoría creada.' : 'No se pudo crear la categoría.');
        return redirect('adminProductos');
    }

    /** Eliminar categoría si no tiene productos asociados */
    public function categoria_delete($IDcategoria) {
        $this->load->model('Categoria_model', 'categorias_m');
        $ID = (int) $IDcategoria;

        // ¿Tiene productos asociados?
        $count = $this->db->where('IDcategoria', $ID)->count_all_results('producto');
        if ($count > 0) {
            $this->session->set_flashdata('error', 'No se puede eliminar: hay productos asociados.');
            return redirect('adminProductos');
        }

        $ok = $this->categorias_m->delete($ID);
        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Categoría eliminada.' : 'No se pudo eliminar la categoría.');
        return redirect('adminProductos');
    }

    /** Guardar nuevo producto */
    public function store() {
        $this->form_validation->set_rules('skuProducto', 'SKU', 'required|alpha_dash');
        $this->form_validation->set_rules('nombre', 'Nombre', 'required|trim|min_length[2]');
        $this->form_validation->set_rules('precio', 'Precio', 'required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('IDcategoria', 'Categoría', 'required|integer');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', strip_tags(validation_errors()));
            return redirect('adminProductos');
        }

        $sku = strtoupper($this->input->post('skuProducto', true));
        if ($this->productos->existsBySku($sku)) {
            $this->session->set_flashdata('error', 'El SKU ya existe.');
            return redirect('adminProductos');
        }

        $imagen = null;
        if (!empty($_FILES['imagen']['name'])) {
            $config = [
                'upload_path' => './assets/img/productos/',
                'allowed_types' => 'jpg|jpeg',
                'max_size' => 2048,
                'file_name' => $sku . '.jpg',
                'overwrite' => true
            ];
            $this->upload->initialize($config);
            if ($this->upload->do_upload('imagen')) {
                $imagen = $sku . '.jpg';
            }
        }

        $data = [
            'skuProducto' => $sku,
            'nombre' => $this->input->post('nombre', true),
            'precio' => $this->input->post('precio', true),
            'peso' => $this->input->post('peso', true),
            'IDcategoria' => $this->input->post('IDcategoria', true),
            'imagen' => $imagen,
        ];

        $ok = $this->productos->create($data);
        $this->session->set_flashdata($ok ? 'success' : 'error', $ok ? 'Producto agregado correctamente.' : 'No se pudo agregar el producto.');
        return redirect('adminProductos');
    }

}
