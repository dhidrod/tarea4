<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class UsuarioController extends Controller
{
    public function index()
    {
        // Creamos la conexión y tenemos acceso a todas las consultas sql del modelo
        $usuarioModel = new UsuarioModel();

        // Se recogen los valores del modelo, ya se pueden usar en la vista
        $usuarios = $usuarioModel->consultaPrueba();

        return $this->view('usuarios.index', $usuarios); // compact crea un array de índice usuarios
    }

    public function create()
    {
        return $this->view('usuarios.create');
    }

    public function store()
    {
        // Volvemos a tener acceso al modelo
        $usuarioModel = new UsuarioModel();

        // Se llama a la función correpondiente, pasando como parámetro
        // $_POST
        var_dump($_POST);
        echo "Se ha enviado desde POST";

        // Podríamos redirigir a donde se desee después de insertar
        //return $this->redirect('/contacts');
    }

    public function show($id)
    {
        echo "Mostrar usuario con id: {$id}";
    }

    public function edit($id)
    {
        echo "Editar usuario";
    }

    public function update($id)
    {
        echo "Actualizar usuario";
    }

    public function destroy($id)
    {
        echo "Borrar usuario";
    }

    public function addUser()
    {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Guardar datos del formulario en sesión (para recuperarlos en caso de error)
        $_SESSION['form_data'] = $_POST;

        // Se instancia el modelo
        $usuarioModel = new UsuarioModel();

        // Se llama a la función correspondiente, pasando como parámetro $_POST
        $resultado = $usuarioModel->addUser($_POST);

        if (!$resultado) {
            // Si hay errores, volver al formulario
            return $this->redirect('/usuario/nuevo');
        } else {
            // Si el registro fue exitoso, redirigir a la página principal
            return $this->redirect('/');
        }
    }

    /*public function panel() // Versión vieja sin ID en la URL
    {
        // Iniciar sesión si no está iniciada
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario está logueado y obtener su ID
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debe iniciar sesión para acceder al panel";
            return $this->redirect('/');
        }
        
        // Se instancia el modelo
        $usuarioModel = new UsuarioModel();
        
        // Obtener el ID del usuario de la sesión
        $id = $_SESSION['user_id'];
        $usuario = $usuarioModel->getUserById($id);
        
        // Si no se encontró el usuario, redirigir a la página principal
        if (!$usuario) {
            $_SESSION["error"] = "Usuario no encontrado";
            return $this->redirect('/');
        } else {
            // Cargar la vista del panel de usuario con los datos del usuario
            //return $this->view('usuarios.panel', ['usuario' => $usuario]);
            return $this->view('usuarios.panel', ['usuario' => $usuario]);
        }
    }*/

    public function panel($userId = null) // Recibir el ID desde la ruta
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

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

        // Verificar si el usuario de la URL coincide con el de la sesión (seguridad)
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $userId) {
            $_SESSION["error"] = "No tienes permiso para acceder a este perfil";
            return $this->redirect('/');
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUserById($userId);

        if (!$usuario) {
            $_SESSION["error"] = "Usuario no encontrado";
            return $this->redirect('/');
        }

        return $this->view('usuarios.panel', ['usuario' => $usuario]);
    }

    // Función para mostrar como fuciona con ejemplos
    public function pruebasSQLQueryBuilder()
    {
        // Se instancia el modelo
        $usuarioModel = new UsuarioModel();
        // Descomentar consultas para ver la creación
        //$usuarioModel->all();
        //$usuarioModel->select('columna1', 'columna2')->get();
        // $usuarioModel->select('columna1', 'columna2')
        //             ->where('columna1', '>', '3')
        //             ->orderBy('columna1', 'DESC')
        //             ->get();
        // $usuarioModel->select('columna1', 'columna2')
        //             ->where('columna1', '>', '3')
        //             ->where('columna2', 'columna3')
        //             ->where('columna2', 'columna3')
        //             ->where('columna3', '!=', 'columna4', 'OR')
        //             ->orderBy('columna1', 'DESC')
        //             ->get();
        $usuarioModel->create(['id' => 1, 'nombre' => 'nombre1']);
        //$usuarioModel->delete(['id' => 1]);
        //$usuarioModel->update(['id' => 1], ['nombre' => 'NombreCambiado']);

        echo "Pruebas SQL Query Builder";
    }
}
