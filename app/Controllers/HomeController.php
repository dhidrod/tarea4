<?php

namespace App\Controllers;

use App\Models\DataBaseModel;
use App\Models\UsuarioModel;
use App\Models\SalaModel;
use App\Models\AsientoModel;

class HomeController extends Controller
{
    // La página principal mostrará un listado de usuarios
    public function index()
    {
        return $this->view('home'); // Seleccionamos una vista (método padre)
    }

    public function error()
    {
        $_SESSION["error"] = "Error en la página solicitada";
        return $this->redirect('/'); // Seleccionamos una vista (método padre)
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
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }
        return $this->view('cine.index');
    }


    // Administración

    public function toAdmin()
    {
        $salaModel = new SalaModel();
        $salas = $salaModel->all()->get();

        return $this->view('admin', ['salas' => $salas]);
    }

    public function toEditSala($id)
    {

        // Creamos una cookie para evitar que se actualicen las entradas cada vez que se carga la página
        if (!isset($_COOKIE['entradasActualizadas'])) {
            setcookie('entradasActualizadas', '1', time() + 60);
            $EntradaController = new EntradaController();
            $EntradaController->updateEntradas();
        }

        // Configuración regional para fechas en español
        setlocale(LC_TIME, 'es_ES.utf8');

        // Parámetros de fecha
        $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
        $año = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');
        $diaSeleccionado = isset($_GET['dia']) ? (int)$_GET['dia'] : date('j');

        // Validación de parámetros
        $mes = max(1, min(12, $mes));
        $año = max(2020, min(2100, $año));
        $fechaActual = strtotime("$año-$mes-01");
        $diasEnMes = date('t', $fechaActual);
        $diaSeleccionado = max(1, min($diasEnMes, $diaSeleccionado));

        // Obtener datos de salas
        if (!isset($salas) || empty($salas)) {
            $salaModel = new SalaModel();
            $salas = $salaModel->where('id', $id)->get();
            $asientoController = new AsientoController();
            $asientoController->setFechaSeleccionada($año, $mes, $diaSeleccionado);
        }

        // Fecha formateada
        $fechaSeleccionada = strftime('%d/%B/%Y', strtotime("$año-$mes-$diaSeleccionado"));

        return $this->view('editsala', ['id' => $id, 'salas' => $salas, 'fechaSeleccionada' => $fechaSeleccionada]);
    }

    public function toEditAsiento($id)
    {

        $AsientoModel = new AsientoModel();
        for ($i = 0; $i <= array_key_last($_POST["asientos"][$id]); $i++) {
            $asientos[$i] = $AsientoModel->all()->where('sala_id', '=', $id)->where('posicion', '=', $_POST["asientos"][$id][$i])->orderBy('id')->get();
        }

        return $this->view('editasiento', ['asientos' => $asientos]);
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

    // Base de datos

    public function makeDatabase()
    {

        $dbModel = new DataBaseModel();
        $result = $dbModel->setupDatabase("tarea4recuperacion22");

        if ($result) {
            $_SESSION["success"] = "Base de datos creada correctamente";
        } else {
            $_SESSION['error'] = "Hubo un error al crear la base de datos.";
        }

        return $this->redirect('/');
    }
}
