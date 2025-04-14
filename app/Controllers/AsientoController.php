<?php

namespace App\Controllers;

use App\Models\AsientoModel;
use App\Models\EntradaModel;

class AsientoController extends Controller
{
    protected $anioActual;
    protected $mesActual;
    protected $diaSeleccionado;

    public function setFechaSeleccionada($anioActual, $mesActual, $diaSeleccionado)
    {
        $this->anioActual = $anioActual;
        $this->mesActual = $mesActual;
        $this->diaSeleccionado = $diaSeleccionado;
    }
    
    public function index()
    {
        // Creamos la conexión y tenemos acceso a todas las consultas sql del modelo
        $AsientoModel = new AsientoModel();

        // Se recogen los valores del modelo, ya se pueden usar en la vista
        $usuarios = $AsientoModel->consultaPrueba();

        return $this->view('cine.index', $usuarios); // compact crea un array de índice usuarios
    }

    public function create()
    {
        return $this->view('usuarios.create');
    }

    public function store()
    {
        // Volvemos a tener acceso al modelo
        $AsientoModel = new AsientoModel();

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

   
    public function editarAsiento(){
        $AsientoModel = new AsientoModel();
        $connection = $AsientoModel->getConnection();
        $connection->beginTransaction(); // Iniciar la transacción
        
        try {
            foreach ($_POST["asientos"] as $index => $asiento) {
                if (!isset($asiento['id']) || !isset($asiento['precio'])) {
                    throw new \Exception("Faltan datos requeridos para el asiento");
                }
                
                $id = $asiento['id'];
                $precio = $asiento['precio'];
                
                // Pasamos los datos como array asociativo al método update
                $resultado = $AsientoModel->update($id, ['precio' => $precio]);
                
                if ($resultado === false) {
                    throw new \Exception("Error al actualizar el asiento con ID: " . $_REQUEST["asientos"][0]["sala_id"]);
                }
            }
        }
        catch (\Exception $e) {
            $_SESSION["error"] = "Error al editar el asiento: " . $e->getMessage();
            $connection->rollBack(); // Deshacer la transacción en caso de error
            return $this->redirect('/admin');
        }

        $_SESSION["success"] = "Asiento editado correctamente";
        $connection->commit(); // Confirmar la transacción
        return $this->redirect('/admin/' . $id);
    }

    public function comprarEntradas($id)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }

        if(!isset($_POST["asientos"])) {
            $_SESSION["error"] = "No has seleccionado asientos";
            return $this->redirect('/cine/'.$id);
        }
        
        $AsientoModel = new AsientoModel();
        
        foreach ($_POST["asientos"] as $id => $asientosPorId) {
            foreach ($asientosPorId as $asiento => $valor) {
                $asiento = $AsientoModel->all()->where('sala_id', $id)->where('posicion', $valor)->get();
                $posicion[] = $asiento[0]['posicion'];
                $precio[] = $asiento[0]['precio'];
                $salas['asiento'] = $posicion;
                $salas['precio'] = $precio;
            }
        }

        $salas['id'] = $id;
        return $this->view('cine.resumen',['salas' => $salas]);

        //echo "array: " .  $_POST["asientos"][$id][0]; //$_POST["asientos"][0][0]
        //echo "array: " .  $_POST["asientos"][$id][1]; //$_POST["asientos"][0][1]
        //echo "array: " .  $_POST["asientos"][$id][2]; //$_POST["asientos"][0][2]
    }

   
    // Función para mostrar como fuciona con ejemplos
    public function pruebasSQLQueryBuilder()
    {
        // Se instancia el modelo
        $AsientoModel = new AsientoModel();
        // Descomentar consultas para ver la creación
        //$AsientoModel->all();
        //$AsientoModel->select('columna1', 'columna2')->get();
        // $AsientoModel->select('columna1', 'columna2')
        //             ->where('columna1', '>', '3')
        //             ->orderBy('columna1', 'DESC')
        //             ->get();
        // $AsientoModel->select('columna1', 'columna2')
        //             ->where('columna1', '>', '3')
        //             ->where('columna2', 'columna3')
        //             ->where('columna2', 'columna3')
        //             ->where('columna3', '!=', 'columna4', 'OR')
        //             ->orderBy('columna1', 'DESC')
        //             ->get();
        $AsientoModel->create(['id' => 1, 'nombre' => 'nombre1']);
        //$AsientoModel->delete(['id' => 1]);
        //$AsientoModel->update(['id' => 1], ['nombre' => 'NombreCambiado']);

        echo "Pruebas SQL Query Builder";
    }

    public function isAsientoOcupado($salaId, $asientoPosicion)
    {
        $AsientoModel = new AsientoModel();
        $asiento = $AsientoModel->all()->where('sala_id', $salaId)->where('posicion', $asientoPosicion)->get();
        
        new EntradaModel();
        $EntradaModel = new EntradaModel();
        $fecha = $EntradaModel->select('fecha_exp')->where('asiento_id', $asiento[0]['id'])->get();
        // Obtener la fecha seleccionada desde la URL
        if (!isset($_GET['año']) || !isset($_GET['mes']) || !isset($_GET['dia'])) {
            // Obtiene la fecha actual
            $fechaSeleccionada = date('Y-m-d');
        } else {
            // Formatear la fecha seleccionada
            $fechaSeleccionada = date('Y-m-d', strtotime($_GET['año'] . '-' . $_GET['mes'] . '-' . $_GET['dia']));
        }
        //$fechaSeleccionada = date('Y-m-d', strtotime($_GET['año'] . '-' . $_GET['mes'] . '-' . $_GET['dia']));
        // Verificar si hay resultados de fecha antes de acceder al índice
        if (!empty($fecha) && isset($fecha[0]['fecha_exp'])) {
            if ($fecha[0]['fecha_exp'] >= $fechaSeleccionada) {
                return true; // Asiento ocupado
            } else {
                // Si la fecha de la entrada es menor a la fecha actual se considera libre
                return false; // Asiento libre
            }
        }

    }


}
