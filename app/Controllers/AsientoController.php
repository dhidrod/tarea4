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
        return $this->redirect('/admin/' . $asiento['sala_id']);
    }

    public function comprarEntradas()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }

        if(!isset($_POST["asientos"])) {
            $_SESSION["error"] = "No has seleccionado asientos";
            return $this->redirect('/cine');
        }
        
        $AsientoModel = new AsientoModel();
        
        foreach ($_POST["asientos"] as $id => $asientosPorId) {
            foreach ($asientosPorId as $asiento => $valor) {
                $asiento = $AsientoModel->all()->where('sala_id', $id)->where('posicion', $valor)->get();
                $posicion[] = $asiento[0]['posicion'];
                $precio[] = $asiento[0]['precio'];
                $salaid[] = $asiento[0]['sala_id'];
                $salas['asiento'] = $posicion;
                $salas['precio'] = $precio;
                $salas['id'] = $salaid;
            }
        }

        
        return $this->view('cine.resumen',['salas' => $salas]);

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
    }    public function isAsientoOcupado($salaId, $asientoPosicion)
    {
        $AsientoModel = new AsientoModel();
        $asiento = $AsientoModel->all()->where('sala_id', $salaId)->where('posicion', $asientoPosicion)->get();
        
        // Si no hay asiento, no puede estar ocupado
        if (empty($asiento)) {
            return false;
        }
        
        $EntradaModel = new EntradaModel();
        
        // Obtener la fecha seleccionada desde la URL
        if (!isset($_GET['año']) || !isset($_GET['mes']) || !isset($_GET['dia'])) {
            // Obtiene la fecha actual
            $fechaSeleccionada = date('Y-m-d');
        } else {
            // Formatear la fecha seleccionada
            $fechaSeleccionada = date('Y-m-d', strtotime($_GET['año'] . '-' . $_GET['mes'] . '-' . $_GET['dia']));
        }
        
        // Buscar entradas para este asiento y fecha específica
        $entrada = $EntradaModel->select('id')
                              ->where('asiento_id', $asiento[0]['id'])
                              ->where('fecha_exp', $fechaSeleccionada)
                              ->get();
        
        // Si hay entrada para esta fecha, el asiento está ocupado
        return !empty($entrada);
    }


}
