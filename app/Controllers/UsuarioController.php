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
        //$usuarioModel->create(['id' => 1, 'nombre' => 'nombre1']);
        //$usuarioModel->delete(['id' => 1]);
        //$usuarioModel->update(['id' => 1], ['nombre' => 'NombreCambiado']);

        echo "Pruebas SQL Query Builder";
    }
}
