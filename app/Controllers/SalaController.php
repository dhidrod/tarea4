<?php

namespace App\Controllers;

use App\Models\SalaModel;

class SalaController extends Controller
{
    public function index()
    {
        // Creamos la conexión y tenemos acceso a todas las consultas sql del modelo
        $SalaModel = new SalaModel();

        // Se recogen los valores del modelo, ya se pueden usar en la vista
        $usuarios = $SalaModel->consultaPrueba();

        return $this->view('cine.index', $usuarios); // compact crea un array de índice usuarios
    }

    public function create()
    {
        return $this->view('usuarios.create');
    }

    public function store()
    {
        // Volvemos a tener acceso al modelo
        $SalaModel = new SalaModel();

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
        //echo "Editar usuario";
        if ($this->checkSession($id)){

            $SalaModel = new SalaModel();
            $usuario = $SalaModel->getUserById($id);
    
            if (!$usuario) {
                $_SESSION["error"] = "Usuario no encontrado";
                return $this->redirect('/');
            }
    
            return $this->view('usuarios.edit', ['usuario' => $usuario]);
            }
        //return $this->view('usuarios.edit');
    }

    public function update($id)
    {
        echo "Actualizar usuario";
    }

    public function destroy($id)
    {
        echo "Borrar usuario";
    }

    public function updateUser($id){
        if ($this->checkSession($id)){
            if (isset($_POST['nombre']) && $this->validarDato($_POST['nombre'], 'nombre')) {
                $nombre = $_POST['nombre'];
            } else {
                $_SESSION["error"] = "Nombre inválido";
            }
            
            if (isset($_POST['apellido1']) && $this->validarDato($_POST['apellido1'], 'nombre')) {
                $apellido1 = $_POST['apellido1'];
            } else {
                $_SESSION["error"] = "Apellido inválido";
            }

            if (isset($_POST['apellido2']) && $this->validarDato($_POST['apellido2'], 'nombre')) {
                $apellido2 = $_POST['apellido2'];
            } else {
                $_SESSION["error"] = "Apellido inválido";
            }

            if (isset($_POST['nick']) && $this->validarDato($_POST['nick'], 'nombre')) {
                $nick = $_POST['nick'];
            } else {
                $_SESSION["error"] = "Nick inválido";
            }

            if (isset($_POST['correo']) && $this->validarDato($_POST['correo'], 'email')) {
                $correo = $_POST['correo'];
            } else {
                $_SESSION["error"] = "Correo inválido";
            }

            if (isset($_POST['fecha_nacimiento']) && $this->validarDato($_POST['fecha_nacimiento'], 'fecha')) {
                $fecha_nacimiento = $_POST['fecha_nacimiento'];
            } else {
                $_SESSION["error"] = "Fecha inválida";
            }

            if ($_POST['password'] === "") {
                $SalaModel = new SalaModel();
                $usuario = $SalaModel->getUserById($id);
                $usuario['password'] = $usuario['password']; // Mantener la contraseña actual
                $password = $usuario['password']; // No se actualiza la contraseña si no se proporciona

            } else {
                
                if (isset($_POST['password']) && $this->validarDato($_POST['password'], 'password')) {
                    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                } else {
                    $_SESSION["error"] = "Contraseña inválida";
                }

                if (isset($_POST['password2']) && $_POST['password'] === $_POST['password2']) {
                    $password2 = $_POST['password2'];
                } else {
                    $_SESSION["error"] = "Las contraseñas no coinciden";
                }
            }


            if(!isset($_SESSION["error"])){
                $SalaModel = new SalaModel();
                $datos = [
                    'nombre' => $nombre ?? null,
                    'apellido1' => $apellido1 ?? null,
                    'apellido2' => $apellido2 ?? null,
                    'nick' => $nick ?? null,
                    'correo' => $correo ?? null,
                    'fecha_nacimiento' => $fecha_nacimiento ?? null,
                    'password' => $password ?? null
                ];

                $resultado = $SalaModel->update($id, $datos);
    

                if (!$resultado) {
                    // Si hay errores, volver al formulario
                    return $this->redirect('/usuario/edit/' . $id);
                } else {
                    // Si el registro fue exitoso, redirigir a la página principal
                    return $this->redirect('/usuario/' . $id);
                }
            } else {
                // Si hay errores, volver al formulario
                return $this->redirect('/usuario/edit/' . $id);
            }

        }
        // Si no hay sesión activa, redirigir a la página de inicio o de inicio de sesión
        return $this->redirect('/');
    }
    
    public function addUser()
    {

        // Guardar datos del formulario en sesión (para recuperarlos en caso de error)
        $_SESSION['form_data'] = $_POST;

        // Se instancia el modelo
        $SalaModel = new SalaModel();

        // Se llama a la función correspondiente, pasando como parámetro $_POST
        $resultado = $SalaModel->addUser($_POST);

        if (!$resultado) {
            // Si hay errores, volver al formulario
            return $this->redirect('/usuario/nuevo');
        } else {
            // Si el registro fue exitoso, redirigir a la página principal
            return $this->redirect('/');
        }
    }

    public function toSala($id){
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }

        // Si la sesión es válida, redirigir a la vista de sala
        return $this->view('cine.sala', ['id' => $id]);
    }

    public function comprarEntradas($id)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }

        foreach ($_POST["asientos"][$id] as $asiento) {
            echo "Asiento: " . $asiento . "<br>";
        }
        //echo "array: " .  $_POST["asientos"][$id][0]; //$_POST["asientos"][0][0]
        //echo "array: " .  $_POST["asientos"][$id][1]; //$_POST["asientos"][0][1]
        //echo "array: " .  $_POST["asientos"][$id][2]; //$_POST["asientos"][0][2]
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

    public function panel($userId = null) // Recibir el ID desde la ruta
    {

        if ($this->checkSession($userId)){

        $SalaModel = new SalaModel();
        $usuario = $SalaModel->getUserById($userId);

        if (!$usuario) {
            $_SESSION["error"] = "Usuario no encontrado";
            return $this->redirect('/');
        }

        return $this->view('usuarios.panel', ['usuario' => $usuario]);
        } /*else {
            // Si no hay sesión activa, redirigir a la página de inicio o de inicio de sesión
            return $this->redirect('/');
        }*/
    }

    public function toSaldo($userId = null) // Recibir el ID desde la ruta
    {
        if ($this->checkSession($userId)){

        $SalaModel = new SalaModel();
        $usuario = $SalaModel->getUserById($userId);

        if (!$usuario) {
            $_SESSION["error"] = "Usuario no encontrado";
            return $this->redirect('/');
        }

        return $this->view('usuarios.saldo', ['usuario' => $usuario]);
        } /*else {
            // Si no hay sesión activa, redirigir a la página de inicio o de inicio de sesión
            return $this->redirect('/');
        }*/
    }

    public function getSaldo($id)
    {
        $SalaModel = new SalaModel();
        $usuario = $SalaModel->getUserById($id);

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

            $SalaModel = new SalaModel();
            $usuario = $SalaModel->getUserById($id);
    
            if (!$usuario) {
                $_SESSION["error"] = "Usuario no encontrado";
                return $this->redirect('/');
            }
    
            // Sumar la cantidad al saldo actual
            $saldoActual = $usuario['saldo'];
            $nuevoSaldo = $saldoActual + $cantidad;
    
            // Actualizar el saldo usando el método del modelo
            $SalaModel->actualizarSaldo($id, $nuevoSaldo);
    
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

            $SalaModel = new SalaModel();
            $usuario = $SalaModel->getUserById($id);
    
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
            $SalaModel->actualizarSaldo($id, $nuevoSaldo);
    
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
        $SalaModel = new SalaModel();
        // Descomentar consultas para ver la creación
        //$SalaModel->all();
        //$SalaModel->select('columna1', 'columna2')->get();
        // $SalaModel->select('columna1', 'columna2')
        //             ->where('columna1', '>', '3')
        //             ->orderBy('columna1', 'DESC')
        //             ->get();
        // $SalaModel->select('columna1', 'columna2')
        //             ->where('columna1', '>', '3')
        //             ->where('columna2', 'columna3')
        //             ->where('columna2', 'columna3')
        //             ->where('columna3', '!=', 'columna4', 'OR')
        //             ->orderBy('columna1', 'DESC')
        //             ->get();
        $SalaModel->create(['id' => 1, 'nombre' => 'nombre1']);
        //$SalaModel->delete(['id' => 1]);
        //$SalaModel->update(['id' => 1], ['nombre' => 'NombreCambiado']);

        echo "Pruebas SQL Query Builder";
    }

    private function validarDato(string $dato, string $tipo): bool
    {
        switch ($tipo) {
            case 'email':
                $patron = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
                break;

            case 'nombre':
                // Permite letras, acentos, espacios, apóstrofes comunes y números (mín 3 caracteres, máximo 15)
                $patron = '/^[\p{L}\p{N}\s\'-]{3,15}$/u';
                break;

            case 'telefono':
                // Valida números internacionales (opcional + al inicio, de 7 a 15 dígitos)
                $patron = '/^\+?[0-9\s\-]{7,15}$/';
                break;

            case 'password':
                // Mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número y un caracter especial
                $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
                break;

            case 'fecha':
                // Formato YYYY-MM-DD con validación de fecha real
                return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $dato) && checkdate((int)substr($dato, 5, 2), (int)substr($dato, 8, 2), (int)substr($dato, 0, 4));

            case 'numero':
                // Números enteros o decimales (incluye negativos)
                $patron = '/^-?\d+([.,]\d+)?$/';
                break;
            case 'code':
                // Código formado por cod- seguido de 1 a 5 números
                $patron = '/^cod-\d{1,5}$/';
                break;
            default:
                return false;
        }

        return (bool) preg_match($patron, $dato);
    }


}
