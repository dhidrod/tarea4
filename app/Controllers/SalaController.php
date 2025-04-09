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


    public function update($id)
    {
        echo "Actualizar usuario";
    }

    public function destroy($id)
    {
        echo "Borrar usuario";
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

        foreach ($_POST["asientos"][$id] as $a) {
            echo "Asiento: " . $a . "<br>";
        }
        //echo "array: " .  $_POST["asientos"][$id][0]; //$_POST["asientos"][0][0]
        //echo "array: " .  $_POST["asientos"][$id][1]; //$_POST["asientos"][0][1]
        //echo "array: " .  $_POST["asientos"][$id][2]; //$_POST["asientos"][0][2]
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
