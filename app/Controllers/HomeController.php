<?php

namespace App\Controllers;

use App\Models\UsuarioModel; // Import the UsuarioModel class

class HomeController extends Controller
{
    // La página principal mostrará un listado de usuarios
    public function index()
    {
        return $this->view('home'); // Seleccionamos una vista (método padre)
    }

    public function toRegistro()
    {
        return $this->view('usuarios.create'); // Seleccionamos una vista (método padre)
    }

    
    public function login()
{
    $usuarioModel = new UsuarioModel();
    $result = $usuarioModel->checkLogin($_POST['nick'], $_POST['password']);
    
    if ($result) {
        $_SESSION['user'] = $result['nick']; // Guarda el nick
        $_SESSION['user_id'] = $result['id']; // Guarda el ID del usuario
        //return $this->redirect('/');
    } else {
        $_SESSION['error'] = "Credenciales incorrectas";
        //return $this->redirect('/login');
    }
    return $this->redirect('/');
}



    // Función para cerrar sesión
    public function logout()
    {
        unset($_SESSION["user"]);
        session_destroy();
        $this->redirect('/');
    }
}
