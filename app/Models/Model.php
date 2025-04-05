<?php

namespace App\Models;

/**
 * Gestiona la conexión de la base de datos e incluye un esquema para
 * un Query Builder. Los return son ejemplo en caso de consultar la tabla
 * usuarios.
 */

class Model
{
    protected $db_host = 'localhost';
    protected $db_user = 'root'; // Las credenciales se deben guardar en un archivo .env
    protected $db_pass = '';
    protected $db_name = 'mvc_database';

    protected $connection;

    protected $query; // Consulta a ejecutar

    protected $select = '*';
    protected $where, $values = [];
    protected $orderBy;

    protected $table; // Definido en la clase hijo

    public function __construct()  // Se puede modificar según montéis la conexión
    {
        $this->connection();
    }

    public function connection()
    {
        // Conexión a la base de datos, por hacer
    }

    // QUERY BUILDER
    // Consultas: 

    // Recibe la cadena de consulta y la ejecuta
    public function query($sql, $data = [], $params = null)
    {

        echo "Consulta: {$sql} <br>"; // borrar, solo para ver ejemplo
        echo "Data: ";
        var_dump($data);
        echo "Params: ";
        var_dump($params);
        echo "<br>";

        // Si hay $data se lanzará una consulta preparada, en otro caso una normal
        // Está configurado para mysqli, cambiar para usar PDO
        if ($data) {
            if ($params == null) {
                // s para string. sssd para 3 strings y un entero. https://www.php.net/manual/es/mysqli-stmt.bind-param.php
                // por ejemplo: $stmt->bind_param('sssd', $code, $language, $official, $percent);
                $params = str_repeat('s', count($data));
            }
            // Sentencia preparada, pasando array como parámetros
            // Cambiar a PDO
            $smtp = $this->connection->prepare($sql);
            $smtp->bind_param($params, ...$data); // con ... el array cambia a variables
            $smtp->execute();

            $this->query = $smtp->get_result();
        } else {
            $this->query = $this->connection->query($sql);
        }

        return $this;
    }

    public function select(...$columns)
    {
        // Separamos el array en una cadena con ,
        $this->select = implode(', ', $columns);

        return $this;
    }

    // Devuelve todos los registros de una tabla
    public function all()
    {
        // La consulta sería
        $sql = "SELECT * FROM {$this->table}";
        // Y se llama a la sentencia
        $this->query($sql)->get();
    }

    // Consulta base a la que se irán añadiendo partes
    public function get()
    {
        if (empty($this->query)) {
            $sql = "SELECT {$this->select} FROM {$this->table}";

            // Se comprueban si están definidos para añadirlos a la cadena $sql
            if ($this->where) {
                $sql .= " WHERE {$this->where}";
            }

            if ($this->orderBy) {
                $sql .= " ORDER BY {$this->orderBy}";
            }

            $this->query($sql, $this->values);
        }
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        $this->query($sql, [$id], 'i');
    }

    // Se añade where a la sentencia con operador específico
    public function where($column, $operator, $value = null, $chainType = 'AND')
    {
        if ($value == null) { // Si no se pasa operador, por defecto =
            $value = $operator;
            $operator = '=';
        }

        // Si ya había algo de antes 
        if ($this->where) {
            $this->where .= " {$chainType} {$column} {$operator} ?";
        } else {
            $this->where = "{$column} {$operator} ?";
        }

        $this->values[] = $value;

        return $this;
    }

    // Se añade orderBy a la sentencia
    public function orderBy($column, $order = 'ASC')
    {
        if ($this->orderBy) {
            $this->orderBy .= ", {$column} {$order}";
        } else {
            $this->orderBy = "{$column} {$order}";
        }

        return $this;
    }

    // Insertar, recibimos un $_GET o $_POST
    public function create($data)
    {
        $columns = array_keys($data); // array de claves del array
        $columns = implode(', ', $columns); // y creamos una cadena separada por ,

        $values = array_values($data); // array de los valores

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES (?" . str_repeat(',?', count($values) - 1) . ")";

        $this->query($sql, $values, $values);

        return $this;
    }

    public function update($id, $data)
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
        }

        $fields = implode(', ', $fields);

        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = ?";

        $values = array_values($data);
        $values[] = $id;

        $this->query($sql, $values);
        return $this;
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        $this->query($sql, [$id], 'i');
    }

    // Para pruebas, devuelve como si fuese unan consulta, borrar
    public function consultaPrueba()
    {
        return [
            ['id' => 1, 'nombre' => 'Nombre1', 'apellido' => 'Apellido1'],
            ['id' => 1, 'nombre' => 'Nombre2', 'apellido' => 'Apellido2'],
            ['id' => 1, 'nombre' => 'Nombre3', 'apellido' => 'Apellido3']
        ];
    }
}
