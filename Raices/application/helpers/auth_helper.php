<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('require_login')) {
  function require_login() {
    $CI =& get_instance();
    $CI->load->library('session');
    if (!$CI->session->userdata('IDusuario')) {
      redirect('login');
      exit;
    }
  }
}

if (!function_exists('require_role')) {
  // $roles: array de IDs de rol permitidos (ej. [1,2])
  function require_role($roles = []) {
    require_login();
    $CI =& get_instance();
    $rol = (int) $CI->session->userdata('IDrol');
    if (!empty($roles) && !in_array($rol, $roles, true)) {
      // 403 minimalista sin romper rutas
      show_error('No autorizado.', 403, 'Acceso denegado');
      exit;
    }
  }
}
