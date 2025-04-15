<?php

namespace App\Models;

class DataBaseModel extends Model
{
    public function createDatabase($nombre = null)
    {
        if ($nombre) {
            $this->db_name = $nombre;
        } else {
            $this->db_name = "tarea4recuperacion2"; // Asegurar que se usa el nombre correcto
        }

        try {
            // Conectar a MySQL sin especificar base de datos
            $dsn = "mysql:host={$this->db_host}";
            $connection = new \PDO($dsn, $this->db_user, $this->db_pass);
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Crear la base de datos si no existe
            $sql = "CREATE DATABASE IF NOT EXISTS {$this->db_name}";
            $connection->exec($sql);

            // Reconectar ya con la base de datos seleccionada
            $this->connection();
            return true;
        } catch (\PDOException $e) {
            echo 'Error al crear la base de datos: ' . $e->getMessage();
            return false;
        }
    }

    public function createTable($tableName, $columns)
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS {$tableName} ({$columns})";
            $this->connection->exec($sql);
            return true;
        } catch (\PDOException $e) {
            echo 'Error al crear la tabla: ' . $e->getMessage();
            return false;
        }
    }

    public function insertTestData()
    {
        try {
            $this->beginTransaction();

            // Crear 100 usuarios de prueba
            for ($i = 1; $i <= 100; $i++) {
                $userData = [
                    'nombre' => "Usuario{$i}",
                    'apellido1' => "Apellido1_{$i}",
                    'apellido2' => "Apellido2_{$i}",
                    'nick' => "user{$i}",
                    'correo' => "usuario{$i}@example.com",
                    'fecha_nacimiento' => date('Y-m-d', mt_rand(strtotime('-90 years'), strtotime('-16 years'))),
                    'password' => password_hash("1234", PASSWORD_DEFAULT),
                    'saldo' => rand(100, 5000) / 10  
                ];

                $this->table = 'usuarios';
                $this->create($userData);
            }
      
            $this->commit();
            return true;
        } catch (\PDOException $e) {
            $this->rollback();
            echo 'Error al insertar datos de prueba: ' . $e->getMessage();
            return false;
        }
    }

    public function dropDatabase($nombre = null)
    {
        if ($nombre) {
            $this->db_name = $nombre;
        }

        try {
            // Conectar a MySQL sin especificar base de datos
            $dsn = "mysql:host={$this->db_host}";
            $connection = new \PDO($dsn, $this->db_user, $this->db_pass);
            $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            // Eliminar la base de datos si existe
            $sql = "DROP DATABASE IF EXISTS {$this->db_name}";
            $connection->exec($sql);
            return true;
        } catch (\PDOException $e) {
            echo 'Error al eliminar la base de datos: ' . $e->getMessage();
            return false;
        }
    }

    public function setupDatabase($nombre = null)
    {
        if (!$nombre) {
            $nombre = "tarea4recuperacion3";
        }

        // Primero, asegurarse de tener una conexión sin base de datos
        $dsn = "mysql:host={$this->db_host}";
        $connection = new \PDO($dsn, $this->db_user, $this->db_pass);
        $connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Borrar la base de datos si existe
        $connection->exec("DROP DATABASE IF EXISTS {$nombre}");
        
        // Guardar esta conexión
        $this->connection = $connection;
        
        // Continuar con la creación como antes
        if (!$this->createDatabase($nombre)) {
            return false;
        }

        // Crear tabla de usuarios
        $usuariosTable = "
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            apellido1 VARCHAR(100) NOT NULL,
            apellido2 VARCHAR(100),
            nick VARCHAR(50) NOT NULL UNIQUE,
            correo VARCHAR(100) NOT NULL UNIQUE,
            fecha_nacimiento DATE NOT NULL,
            password VARCHAR(255) NOT NULL,
            saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00
        ";
        if (!$this->createTable("usuarios", $usuariosTable)) {
            return false;
        }

        $salasTable = "
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            capacidad INT NOT NULL
        ";
        if (!$this->createTable("salas", $salasTable)) {
            return false;
        }

        $asientosTable = "
            id INT AUTO_INCREMENT PRIMARY KEY,
            sala_id INT NOT NULL,
            posicion VARCHAR(10) NOT NULL,
            precio DECIMAL(6,2) NOT NULL,
            FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE,
            UNIQUE KEY (sala_id, posicion)
        ";
        if (!$this->createTable("asientos", $asientosTable)) {
            return false;
        }

        $entradasTable = "
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT NOT NULL,
            asiento_id INT NOT NULL,
            fecha_compra DATE NOT NULL,
            fecha_exp DATE NOT NULL,
            precio_compra DECIMAL(6,2) NOT NULL,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
            FOREIGN KEY (asiento_id) REFERENCES asientos(id) ON DELETE CASCADE
        ";
        if (!$this->createTable("entradas", $entradasTable)) {
            return false;
        }

        $cuentaCineTable = "
            id INT AUTO_INCREMENT PRIMARY KEY,
            saldo DECIMAL(10,2) NOT NULL DEFAULT 0.00
        ";
        if (!$this->createTable("cuenta_cine", $cuentaCineTable)) {
            return false;
        }
        
        // Insertamos un registro inicial en cuenta_cine
        if (!$this->query("INSERT INTO cuenta_cine (saldo) VALUES (0.00)")) {
            return false;
        }

        if (!$this->query("CREATE INDEX idx_asientos_sala ON asientos(sala_id)")) {
            return false;
        }
        if (!$this->query("CREATE INDEX idx_entradas_usuario ON entradas(usuario_id)")) {
            return false;
        }
        if (!$this->query("CREATE INDEX idx_entradas_asiento ON entradas(asiento_id)")) {
            return false;
        }
        

        // Insertar los datos de prueba
        if (!$this->insertTestData()) {
            return false;
        }

        return true;
    }
}
