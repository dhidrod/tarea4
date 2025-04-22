<?php

namespace App\Controllers;

use App\Models\SalaModel;
use App\Models\AsientoModel;
use App\Models\EntradaModel;

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
        $connection = $SalaModel->getConnection();


        if ($this->validarDato($_POST["nombre"], 'nombre') == false) {
            $_SESSION["error"] = "El nombre de la sala no tiene un formato válido<br>";
        }
        if ($this->validarDato($_POST["capacidad"], 'numero') == false) {
            $_SESSION["error"] = "La capacidad no tiene un formato válido<br>";
        }

        if (isset($_SESSION["error"])) {
            return $this->redirect('/admin');
        }
        $connection->beginTransaction();

        try {
            // Primero creamos la sala
            $data = ["nombre" => $_POST["nombre"], "capacidad" => $_POST["capacidad"]];
            $SalaModel->create($data);

            // Luego creamos los asientos
            $salaId = $connection->lastInsertId(); // Obtener el ID de la sala recién creada

            $asientoModel = new AsientoModel($connection);

            for ($i = 1; $i <= $_POST["capacidad"]; $i++) {
                $data = ["sala_id" => $salaId, "posicion" => $i, "precio" => 5];
                $asientoModel->create($data);
            }
        } catch (\Exception $e) {
            $connection->rollBack(); // Deshacer la transacción en caso de error

            $_SESSION["error"] = "Error al crear la sala: " . $e->getMessage();

            return $this->redirect('/admin');
        }

        $connection->commit();

        $_SESSION["success"] = "Sala creada correctamente";

        return $this->redirect('/admin');
    }


    public function toSalaOLD()
    {

        $id = isset($_GET['p']) ? $_GET['p'] : 1;

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

    public function toSala()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }

        // Update entries with cookie check
        if (!isset($_COOKIE['entradasActualizadas'])) {
            setcookie('entradasActualizadas', '1', time() + 60);
            $entradaController = new EntradaController();
            $entradaController->updateEntradas();
        }

        // Configure regional settings for Spanish dates
        setlocale(LC_TIME, 'es_ES.utf8');

        // Get pagination configuration
        $configPaginacion = include __DIR__ . '/../../config/paginacion.php';
        $elementosPorPagina = $configPaginacion['salas_por_pagina'] ?? 1;

        // Get and validate date parameters
        $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
        $año = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');
        $diaSeleccionado = isset($_GET['dia']) ? (int)$_GET['dia'] : date('j');

        // Validate date parameters
        $mes = max(1, min(12, $mes));
        $año = max(2020, min(2100, $año));
        $fechaActual = strtotime("$año-$mes-01");
        $diasEnMes = date('t', $fechaActual);
        $diaSeleccionado = max(1, min($diasEnMes, $diaSeleccionado));

        // Get pagination parameter
        $paginaActual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $paginaActual = max(1, $paginaActual);

        // Create model and get total count
        $salaModel = new SalaModel();
        $totalSalas = count($salaModel->all()->get());

        // Calculate pagination
        $totalPaginas = ceil($totalSalas / $elementosPorPagina);
        $paginaActual = min($paginaActual, max(1, $totalPaginas));

        // Get only the needed salas for current page
        $offset = ($paginaActual - 1) * $elementosPorPagina;
        $salas = $salaModel->paginate($elementosPorPagina, $offset);

        // Setup AsientoController if we have rooms
        $asientoController = null;
        if (!empty($salas)) {
            $asientoController = new AsientoController();
            $asientoController->setFechaSeleccionada($año, $mes, $diaSeleccionado);
        }

        // Format selected date
        $fechaSeleccionada = strftime('%d/%B/%Y', strtotime("$año-$mes-$diaSeleccionado"));

        // Create data array to pass to view
        $data = [
            'salas' => $salas,
            'asientoController' => $asientoController,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'totalSalas' => $totalSalas,
            'elementosPorPagina' => $elementosPorPagina,
            'mes' => $mes,
            'año' => $año,
            'diaSeleccionado' => $diaSeleccionado,
            'fechaSeleccionada' => $fechaSeleccionada,
            'fechaActual' => $fechaActual,
            'diasEnMes' => $diasEnMes
        ];
        // vamos a crear un array con todos los asientos ocupados que existan en la base de datos, para ello vamos a buscar en la table entradas y vamos a asignar los asientos

        $entradaModel = new EntradaModel();
        $entradas = $entradaModel->all()->get(); 
        $asientosOcupados = [];
        
        $asientoModel = new AsientoModel();
        foreach ($entradas as $entrada) {
            $asiento = $asientoModel->all()->where('id', '=', $entrada['asiento_id'])->get();
            if ($asiento) {
                $asientosOcupados[] = [
                    'sala_id' => $asiento[0]['sala_id'],
                    'posicion' => $asiento[0]['posicion']
                ];
            }
        }
        // Añadimos los asientos ocupados al array de datos para la vista
        $data['asientosOcupados'] = $asientosOcupados;
        return $this->view('cine.sala', $data);
    }
}
