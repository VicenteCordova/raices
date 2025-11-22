<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Crear_usuario extends CI_Controller {

    public function crear_usuario_demo() {
     
        $rut = '19346338';
        $nombre  = 'Pedro';
        $a_paterno = 'Luengo';
        $a_materno = 'Vasquez';
        $e_mail = 'p_luengo19@hotmail.com';
        $password = '123';
        $IDestado = 1;
        $IDcargo = 1;
        $IDrol = 1;

        // Hashear contraseña
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->db->query(
                "INSERT INTO usuario (rut, nombre, a_paterno, a_materno, e_mail, password, IDestado, IDcargo, IDrol)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [$rut, $nombre, $a_paterno, $a_materno, $e_mail, $passwordHash, $IDestado, $IDcargo, $IDrol]
        );

        echo "Usuario insertado correctamente usando procedimiento.";
    }

}
