<?php

namespace App\Models;

class UsuarioModel extends Model
{
    // Nombre de la tabla que se realizarán las consultas
    protected $table = 'usuarios';

    // Aquí también se podría definir las consultas que son específicas
    // para los usuarios. Para las demás llamaremos a los métodos de la
    // clase padre.
    // También se podría configurar la conexión para que la información se 
    // recuperase de otra base de datos, otro usuario, etc. cambiando:
    // protected $db_host = 'localhost';
    // protected $db_user = 'root';
    // protected $db_pass = '';
    // protected $db_name = 'mvc_database'; 

    public function checkLogin($nick, $password)
    {
        // Primero busca el usuario por su nick
        $sql = "SELECT * FROM {$this->table} WHERE nick = ?";
        $user = $this->query($sql, [$nick])->get();

        // Si encontramos un usuario y la contraseña coincide
        if (!empty($user) && isset($user[0])) {
            // Si las contraseñas están hasheadas (recomendado)
            if (password_verify($password, $user[0]['password'])) {
                return $user[0]; // Devuelve los datos del usuario
            }
            // Si las contraseñas están en texto plano (no recomendado)
            else if ($user[0]['password'] === $password) {
                return $user[0]; // Devuelve los datos del usuario
            }
        }

        return false; // Usuario no encontrado o contraseña incorrecta
    }

    public function addUser($data)
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


        $apellido2 = $data['apellido2'] ?? '';

        if (!$this->validarDato($apellido2, 'nombre')) {
            $errores[] = 'El segundo apellido no tiene un formato válido<br>';
        }


        $nick = $data['nick'] ?? '';

        if (!$this->validarDato($nick, 'nombre')) {
            $errores[] = 'El nick no tiene un formato válido<br>';
        }


        $correo = $data['correo'] ?? '';

        if (!$this->validarDato($correo, 'email')) {
            $errores[] = 'El correo electrónico no tiene un formato válido<br>';
        }


        $fecha_nacimiento = $data['fecha_nacimiento'] ?? '';

        if (!$this->validarDato($fecha_nacimiento, 'fecha')) {
            $errores[] = 'La fecha de nacimiento no tiene un formato válido<br>';
        }

        
        $password = $data['password'] ?? '';

        if (!$this->validarDato($password, 'password')) {
            $errores[] = 'La contraseña no tiene un formato válido<br>';
        }


        // Verificar si existe password2 para comparar
        $password2 = $data['password2'] ?? ($data['password_confirm'] ?? '');
        
        // Validar que las contraseñas coincidan
        if ($data['password'] !== $password2) {
            $errores[] = 'Las contraseñas no coinciden<br>';
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
