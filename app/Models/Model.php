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
        try {

            if (file_exists(__DIR__ . '/../../../vendor/autoload.php')) {
                require_once __DIR__ . "/../../../vendor/autoload.php";
                $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
                $dotenv->load();


                $this->db_host = $_ENV['DB_HOST'] ?? $this->db_host;
                $this->db_name = $_ENV['DB_NAME'] ?? $this->db_name;
                $this->db_user = $_ENV['DB_USER'] ?? $this->db_user;
                $this->db_pass = $_ENV['DB_PASS'] ?? $this->db_pass;
            }

            $dsn = "mysql:host={$this->db_host};dbname={$this->db_name}";
            $this->connection = new \PDO($dsn, $this->db_user, $this->db_pass);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

            // echo 'Salida (fuera del catch): ';

        } catch (\PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
            die();
        }
    }

    // QUERY BUILDER
    // Consultas: 

    // Recibe la cadena de consulta y la ejecuta
    public function query($sql, $data = [], $params = null)
    {
        echo "Consulta: {$sql} <br>"; // borrar, solo para ver ejemplo
        echo "Data: ";
        var_dump($data);
        echo "<br>";

        try {
            if (!empty($data)) {
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($data);
                $this->query = $stmt;
            } else {
                $this->query = $this->connection->query($sql);
            }

            return $this;
        } catch (\PDOException $e) {
            echo "Error en la consulta: " . $e->getMessage();
            die();
        }
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

            if ($this->where) {
                $sql .= " WHERE {$this->where}";
            }

            if ($this->orderBy) {
                $sql .= " ORDER BY {$this->orderBy}";
            }

            $this->query($sql, $this->values);
        }

        // Devolver los resultados
        if ($this->query instanceof \PDOStatement) {
            return $this->query->fetchAll();
        }

        return [];
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
