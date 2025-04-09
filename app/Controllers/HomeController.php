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

// Usuario

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

// Cine

    public function toCine()
    {
        if ($this->checkSession($_SESSION['user_id']) === false) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }
        return $this->view('cine.index');
    }





    private function checkSession($userId)
    {
        // Obtener el ID desde la URL si no se pasa como parámetro
        if ($userId === null) {
            // Extraer el ID de la URL (ej: "/usuario/2" -> 2)
            $requestUri = $_SERVER['REQUEST_URI'];
            $segments = explode('/', $requestUri);
            $userId = end($segments);
        }

        // Validar que el ID sea numérico
        if (!is_numeric($userId)) {
            $_SESSION["error"] = "ID de usuario inválido";
            return $this->redirect('/');
        }

        // Comprueba si hay una sesión activa
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }

        // Verificar si el usuario de la URL coincide con el de la sesión (seguridad)
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $userId) {
            $_SESSION["error"] = "No tienes permiso para acceder a este perfil";
            return $this->redirect('/');
        }

        return true; // La sesión es válida y el usuario tiene permiso

    }
}
