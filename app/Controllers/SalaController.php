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
            // Validar capacidad
            if ($_POST["capacidad"] < 1) {
                throw new \Exception("La capacidad debe ser mayor que 0.");
            }
            if ($_POST["capacidad"] > 100) {
                throw new \Exception("La capacidad no puede ser mayor que 100.");
            }

            // validar nombre
            $nombre = filter_var($_POST["nombre"], FILTER_UNSAFE_RAW);
            if (empty($nombre)) {
                throw new \Exception("El nombre de la sala no puede estar vacío.");
            }
            if (strlen($nombre) > 50) {
                throw new \Exception("El nombre de la sala no puede exceder los 50 caracteres.");
            }

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
    // Verificar si el usuario ha iniciado sesión
    if (!isset($_SESSION['user_id'])) {
        $_SESSION["error"] = "Debes iniciar sesión primero";
        return $this->redirect('/');
    }

    // Actualizar entradas si no se ha hecho recientemente (verificado con una cookie)
    if (!isset($_COOKIE['entradasActualizadas'])) {
        setcookie('entradasActualizadas', '1', time() + 60);
        $entradaController = new EntradaController();
        $entradaController->updateEntradas();
    }

    // Configurar la localización regional para fechas en español
    setlocale(LC_TIME, 'es_ES.utf8');

    // Obtener la configuración de paginación
    $configPaginacion = include __DIR__ . '/../../config/paginacion.php';
    $elementosPorPagina = $configPaginacion['salas_por_pagina'] ?? 1;

    // Obtener y validar los parámetros de fecha
    $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
    $año = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');
    $diaSeleccionado = isset($_GET['dia']) ? (int)$_GET['dia'] : date('j');

    // Validar que los parámetros de fecha estén dentro de rangos aceptables
    $mes = max(1, min(12, $mes));
    $año = max(2020, min(2100, $año));
    $fechaActual = strtotime("$año-$mes-01");
    $diasEnMes = date('t', $fechaActual);
    $diaSeleccionado = max(1, min($diasEnMes, $diaSeleccionado));

    // Obtener y validar el parámetro de paginación
    $paginaActual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $paginaActual = max(1, $paginaActual);

    // Crear el modelo y contar el total de salas
    $salaModel = new SalaModel();
    $totalSalas = count($salaModel->all()->get());

    // Calcular los valores para la paginación
    $totalPaginas = ceil($totalSalas / $elementosPorPagina);
    $paginaActual = min($paginaActual, max(1, $totalPaginas));

    // Obtener solo las salas necesarias para la página actual
    $offset = ($paginaActual - 1) * $elementosPorPagina;
    $salas = $salaModel->paginate($elementosPorPagina, $offset);

    // Configurar el controlador de asientos si hay salas disponibles
    $asientoController = null;
    if (!empty($salas)) {
        $asientoController = new AsientoController();
        $asientoController->setFechaSeleccionada($año, $mes, $diaSeleccionado);
    }

    // Formatear la fecha seleccionada para mostrarla
    $fechaSeleccionada = strftime('%d/%B/%Y', strtotime("$año-$mes-$diaSeleccionado"));

    // Crear un array con los datos que se enviarán a la vista
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

    // Crear un array con todos los asientos ocupados existentes en la base de datos.
    // Para ello, buscamos en la tabla entradas y asignamos los asientos correspondientes.
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

    // Añadir los asientos ocupados al array de datos que se enviará a la vista
    $data['asientosOcupados'] = $asientosOcupados;
    return $this->view('cine.sala', $data);
}

}
