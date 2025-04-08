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

    public function toSaldo($userId = null) // Recibir el ID desde la ruta
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

        return $this->view('usuarios.saldo', ['usuario' => $usuario]);
    }

    public function getSaldo($id)
    {
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUserById($id);

        if ($usuario && isset($usuario['saldo'])) {
            return $usuario['saldo'];
        }
        return false; // o un mensaje de error
    }


    public function addSaldo($id)
    {
        // Verificar que la petición sea POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Convertir la cantidad recibida a un valor numérico
            $cantidad = floatval($_POST['cantidad']);

            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->getUserById($id);
    
            if (!$usuario) {
                $_SESSION["error"] = "Usuario no encontrado";
                return $this->redirect('/');
            }
    
            // Sumar la cantidad al saldo actual
            $saldoActual = $usuario['saldo'];
            $nuevoSaldo = $saldoActual + $cantidad;
    
            // Actualizar el saldo usando el método del modelo
            $usuarioModel->actualizarSaldo($id, $nuevoSaldo);
    
            // Redirigir al panel de usuario u otra página
            return $this->redirect('/usuario/' . $id);
        }
    
        // Si no es POST, redirigir o mostrar un error
        return $this->redirect('/');
    }
    

    public function subtractSaldo($id)
    {
        // Verificar que la petición sea POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Convertir la cantidad recibida a un valor numérico
            $cantidad = floatval($_POST['cantidad']);

            $usuarioModel = new UsuarioModel();
            $usuario = $usuarioModel->getUserById($id);
    
            if (!$usuario) {
                $_SESSION["error"] = "Usuario no encontrado";
                return $this->redirect('/');
            }
    
            // Sumar la cantidad al saldo actual
            $saldoActual = $usuario['saldo'];
            
            // Verificar que el saldo no sea negativo después de la resta
            if ($saldoActual - $cantidad < 0) {
                $_SESSION["error"] = "Saldo insuficiente";
                return $this->redirect('/usuario/' . $id);
            }
            $nuevoSaldo = $saldoActual - $cantidad;
    
            // Actualizar el saldo usando el método del modelo
            $usuarioModel->actualizarSaldo($id, $nuevoSaldo);
    
            // Redirigir al panel de usuario u otra página
            return $this->redirect('/usuario/' . $id);
        }
    
        // Si no es POST, redirigir o mostrar un error
        return $this->redirect('/');
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
