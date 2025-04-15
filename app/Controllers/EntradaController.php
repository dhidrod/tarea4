<?php

namespace App\Controllers;

use App\Models\AsientoModel;
use App\Models\EntradaModel;
use App\Models\UsuarioModel;
use App\Models\CineModel;
use App\Models\Model;

class EntradaController extends Controller
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


    public function toSala($id)
    {
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

        if (!isset($_POST["asientos"])) {
            $_SESSION["error"] = "No has seleccionado asientos";
            return $this->redirect('/cine/' . $id);
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
        return $this->view('cine.resumen', ['salas' => $salas]);

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

    public function terminarCompra()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION["error"] = "Debes iniciar sesión primero";
            return $this->redirect('/');
        }

        // Creamos la instancia base para la transacción
        $db = new Model();
        $connection = $db->getConnection();

        if (!$connection) {
            $_SESSION["error"] = "Error al conectar con la base de datos";
            return $this->redirect('/cine/' . $_POST['sala_id']);
        }

        // Creamos todos los modelos con la misma conexión
        $EntradaModel = new EntradaModel($connection);
        $usuarioModel = new UsuarioModel($connection);
        $cineModel = new CineModel($connection);
        $AsientoModel = new AsientoModel($connection);

        //$EntradaModel = new EntradaModel();
        //$usuarioModel = new UsuarioModel();
        // Primero comprobamos el saldo del usuario, para ver si tiene dinero para comprar las entradas.
        // Si no tiene saldo, redirigir a la vista de saldo insuficiente
        $saldo = $usuarioModel->select('saldo')->where('id', $_SESSION['user_id'])->get();
        if ($saldo[0]['saldo'] < $_POST['precio_total']) {
            $_SESSION["error"] = "Saldo insuficiente para realizar la compra";
            return $this->redirect('/cine/' . $_POST['sala_id']);
        } else {
            try {
                // Creamos un punto de rollback para que si hay un error, se pueda deshacer la transacción
                $connection->beginTransaction();

                // Restamos el precio total al saldo del usuario
                $nuevoSaldo = $saldo[0]['saldo'] - $_POST['precio_total'];
                $result = $usuarioModel->update(['id' => $_SESSION['user_id']], ['saldo' => $nuevoSaldo]);

                if (!$result) {
                    throw new \Exception("Error al actualizar el saldo del usuario");
                }
                // Y actualizamos el saldo del cine.
                //$cineModel = new CineModel();
                $saldoCine = $cineModel->select('saldo')->where('id', 1)->get();
                $nuevoSaldo = $saldoCine[0]['saldo'] + $_POST['precio_total'];
                $result = $cineModel->update(['id' => 1], ['saldo' => $nuevoSaldo]);

                if (!$result) {
                    throw new \Exception("Error al transferir el saldo al cine");
                }

                //$AsientoModel = new AsientoModel();
                // Ahora creamos la entrada en la base de datos. Por cada asiento creamos una entrada.
                foreach ($_POST["asientos"] as $asientosPorId) {
                    // Busca la id del asiento
                    $idAsiento = $AsientoModel->select('id')->where('sala_id', $_POST['sala_id'])->where('posicion', $asientosPorId)->get();
                    $asientosPorId = $idAsiento[0]['id'];
                    $precioAsiento = $AsientoModel->select('precio')->where('id', $asientosPorId)->get();
                    // Creamos un array con todos los datos para la entrada
                    $entrada = [
                        'usuario_id' => $_SESSION['user_id'],
                        'asiento_id' => $asientosPorId,
                        'precio_compra' => $precioAsiento[0]['precio'],
                        'fecha_exp' => $_POST["fecha_seleccionada"]
                    ];
                    $result = $EntradaModel->create($entrada);
                }
                // Si la entrada se ha creado correctamente, actualizamos el asiento como vendido
                if (!$result) {
                    throw new \Exception("Error al crear la entrada");
                }

                // Redirigimos a la vista de compra realizada con éxito
                $_SESSION["success"] = "Compra realizada con éxito";
                $connection->commit();
                return $this->redirect('/cine');
            } catch (\Exception $e) {
                // Si hay un error, se hace un rollback y se redirige a la vista de error
                $_SESSION["error"] = "Error al realizar la compra: " . $e->getMessage();
                $connection->rollback();
                return $this->redirect('/cine/' . $_POST['sala_id']);
            }
        }
    }

    public function updateEntradas(): void
    {
        $entradaModel = new EntradaModel();
        $fecha_actual = date('Y-m-d');
        $entradas = $entradaModel->all()->where('fecha_exp', '<', $fecha_actual)->get();
        foreach ($entradas as $entrada) {
            $entradaModel->delete(['id' => $entrada['id']]);
        }
    }
}
