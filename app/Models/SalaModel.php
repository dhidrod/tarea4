<?php

namespace App\Models;

class SalaModel extends Model
{
    // Nombre de la tabla que se realizarán las consultas
    protected $table = 'salas';

    // Aquí también se podría definir las consultas que son específicas
    // para los usuarios. Para las demás llamaremos a los métodos de la
    // clase padre.
    // También se podría configurar la conexión para que la información se 
    // recuperase de otra base de datos, otro usuario, etc. cambiando:
    // protected $db_host = 'localhost';
    // protected $db_user = 'root';
    // protected $db_pass = '';
    // protected $db_name = 'mvc_database'; 


    public function addSala($data)
    {

        $errores = [];

        // Validar los datos de entrada

        $nombre = $data['nombre'] ?? '';

        if (!$this->validarDato($nombre, 'nombre')) {
            $errores[] = 'El nombre de usuario no tiene un formato válido<br>';
        }


        $apellido1 = $data['apellido1'] ?? '';

        if (!$this->validarDato($apellido1, 'nombre')) {
            $errores[] = 'El primer apellido no tiene un formato válido<br>';
        }



        // Verificar si el nick ya existe
        $existingUser = $this->where('nick', $data['nick'])->get();
        if (!empty($existingUser)) {
            $errores[] = 'Ese nick ya está en uso<br>';
            //return ['error' => 'El nombre de usuario ya está en uso'];
            return false;
        }

        // Verificar si el correo ya existe
        $existingEmail = $this->where('correo', $data['correo'])->get();
        if (!empty($existingEmail)) {
            $errores[] = 'Ese correo ya está registrado<br>';
            //return ['error' => 'El correo electrónico ya está registrado'];
        }

        // Si hay errores, devolverlos
        if (!empty($errores)) {
            $_SESSION["error"] = implode('', $errores);
        } else {

            // Preparar los datos para insertar
            $userData = [
                'nombre' => $data['nombre'],
                'apellido1' => $data['apellido1'],
                'apellido2' => $data['apellido2'],
                'nick' => $data['nick'],
                'correo' => $data['correo'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT), // Hashear la contraseña
                'saldo' => 0 // Asignar un saldo inicial de 0
            ];

            // Insertar el usuario
            $this->create($userData);

            return true;
        }
    }


    public function getSalaById($id)
    {
        // Verificar que el ID sea válido
        if (!is_numeric($id) || $id <= 0) {
            return null;
        }

        // Primero ejecutamos find() que prepara la consulta
        $this->find($id);
        // Luego obtenemos los resultados con get()
        $result = $this->get();
        //$result = $this->find($id)->get();
        // Verificar si se encontró el usuario
        if (!empty($result) && isset($result[$id-1])) {
            return $result[$id-1]; // Devolver el usuario encontrado
        }

        return null; // Devolver null si no hay resultados
    }
    
}
