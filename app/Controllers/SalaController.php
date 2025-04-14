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

    public function addSala()
    {
        $SalaModel = new SalaModel();

        if ($this->validarDato($_POST["nombre"], 'nombre') == false) {
            $_SESSION["error"] = "El nombre de la sala no tiene un formato válido<br>";     
        }
        if ($this->validarDato($_POST["capacidad"], 'numero') == false) {
            $_SESSION["error"] = "La capacidad no tiene un formato válido<br>";
        }

        if (isset($_SESSION["error"])) {
            return $this->redirect('/admin');
        }

        $data = ["nombre" => $_POST["nombre"], "capacidad" => $_POST["capacidad"]];
        $SalaModel->create($data);
        $_SESSION["success"] = "Sala creada correctamente";
        return $this->redirect('/admin');
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


}
