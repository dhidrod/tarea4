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
        return $this->view('registro'); // Seleccionamos una vista (método padre)
    }

    // Función para logear al usuario
    /*public function login()
    {
            $user = isset($_POST['nombre']) ? strtolower($_POST['nombre']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            // Comprobamos si el usuario y la contraseña coinciden con alguno de los usuarios registrados
    //foreach ($datos as $dato) {
    //    if ($dato['nombre'] === $user && password_verify($password, $dato['password'])) {
    //        $_SESSION['user'] = $user;
    //        break;

            if ($user === "admin" && $password === "1234") {
                $_SESSION['user'] = "admin";
            } else {
                $_SESSION['error'] = 'Usuario o contraseña incorrectos';
            }
        //}
    //}
        header("Location: /");
        //return $this->view('home', ['error' => $error]);
        
    }*/



    public function login()
    {

        // Ajusta los nombres de los campos según tu formulario
        $nickname = $_POST['nick'] ?? ''; // o podría ser 'nombre', 'username', etc.
        $password = $_POST['password'] ?? ''; // o podría ser 'pass', 'clave', etc.

        // Instanciamos el modelo
        $usuarioModel = new UsuarioModel();

        // Verificamos las credenciales
        $user = $usuarioModel->checkLogin($nickname, $password);

        // Si el usuario existe y la contraseña es correcta
        if ($user) {
            // Iniciar sesión si no está iniciada
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION['user'] = $user['nick'];

            // Redirigir a la página principal
            return $this->redirect('/');
        } else {
            // Si las credenciales son incorrectas
            $_SESSION['error'] = 'Usuario o contraseña incorrectos';
            return $this->redirect('/');
        }
    }



    // Función para cerrar sesión
    public function logout()
    {
        unset($_SESSION["user"]);
        session_destroy();
        header("Location: /");
        exit();
    }
}
