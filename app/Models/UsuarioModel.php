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
}
