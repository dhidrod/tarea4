<?php

namespace App\Controllers;

class HomeController extends Controller
{
    // La página principal mostrará un listado de usuarios
    public function index()
    {
        return $this->view('home'); // Seleccionamos una vista (método padre)
    }

    // Función para logear al usuario
    public function login()
    {
            $user = isset($_POST['nombre']) ? strtolower($_POST['nombre']) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            // Comprobamos si el usuario y la contraseña coinciden con alguno de los usuarios registrados
    /*foreach ($datos as $dato) {
        if ($dato['nombre'] === $user && password_verify($password, $dato['password'])) {
            $_SESSION['user'] = $user;
            break;*/

            if ($user === "admin" && $password === "1234") {
                $_SESSION['user'] = "admin";
            } else {
                $_SESSION['error'] = 'Usuario o contraseña incorrectos';
            }
        //}
    //}
        header("Location: /");
        //return $this->view('home', ['error' => $error]);
        
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
