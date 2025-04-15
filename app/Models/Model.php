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

    protected $ownConnection = true; // Si se quiere usar la conexión de otro modelo, se puede cambiar a false

    public function __construct($existingConnection = null)
    {
        if ($existingConnection) {
            $this->connection = $existingConnection;
            $this->ownConnection = false;
        } else {
            $this->connection();
            $this->ownConnection = true;
        }
    }

    
    public function getConnection()
    {
        return $this->connection;
    }

    public function connection()
    {
        try {

            if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
                require_once __DIR__ . "/../../vendor/autoload.php";
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
            // Return the PDO connection to allow rollback in the controller
           // return $this->connection;

        } catch (\PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
            die();
        }
    }



    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollback()
    {
        return $this->connection->rollBack();
    }


    // QUERY BUILDER
    // Consultas: 

    // Recibe la cadena de consulta y la ejecuta
    // QUERY BUILDER
    // Consultas: 

    // Recibe la cadena de consulta y la ejecuta
    public function query($sql, $data = [], $params = null)
    {
        /*echo "Consulta: {$sql} <br>"; // borrar, solo para ver ejemplo
        echo "Data: ";
        var_dump($data);
        echo "Params: ";
        var_dump($params);
        echo "<br>";*/

        try {
            // Si hay $data se lanzará una consulta preparada, en otro caso una normal
            if ($data) {
                // Preparar la consulta con PDO
                $stmt = $this->connection->prepare($sql);

                // Ejecutar con array de parámetros
                if (is_array($data)) {
                    $stmt->execute($data);
                } else {
                    $stmt->execute([$data]);
                }

                $this->query = $stmt;
            } else {
                // Consulta directa sin parámetros
                $this->query = $this->connection->query($sql);
            }

            return $this; // Devolvemos this para permitir encadenamiento de métodos

        } catch (\PDOException $e) {
            // Manejo de errores
            echo "Error en la consulta: " . $e->getMessage();
            return false;
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
        return $this->query($sql); // Añadido return
    }

    // Método para obtener los resultados después de ejecutar la consulta
    public function getResults()
    {
        if ($this->query) {
            return $this->query->fetchAll(\PDO::FETCH_ASSOC);
        }
        return [];
    }

    // Consulta base a la que se irán añadiendo partes
    public function get()
    {
        $sql = "SELECT {$this->select} FROM {$this->table}";

        // Se comprueban si están definidos para añadirlos a la cadena $sql
        if ($this->where) {
            $sql .= " WHERE {$this->where}";
        }

        if ($this->orderBy) {
            $sql .= " ORDER BY {$this->orderBy}";
        }

        // Ejecutamos la consulta y devolvemos los resultados directamente
        $this->query($sql, $this->values);

        // Limpiamos las propiedades para la siguiente consulta
        $this->select = '*';
        $this->where = null;
        $this->values = [];
        $this->orderBy = null;

        // Devolvemos los resultados
        return $this->getResults();
    }

    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";

        $this->query($sql, [$id]); // Añadido return y eliminado el tercer parámetro ya que no se usa.
    }

    // Se añade where a la sentencia con operador específico

    // where('id', 1)
    // where('id', '=', 1, 'AND')


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

        /*$this->query($sql, $values, $values);

        return $this;*/
        return $this->query($sql, $values, $values);
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
        //$values[] = $id;
        // Esto es nuevo, intenta arreglar un bug donde no se pasaba el id si no un array con el id dentro
        if (is_array($id)) {
            $id = reset($id); // Optiene el primer valor del array
        }
        $values[] = $id;

        /*$this->query($sql, $values);
        return $this;*/
        return $this->query($sql, $values);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";

        // Esto es nuevo, intenta arreglar un bug donde no se pasaba el id si no un array con el id dentro
        if (is_array($id)) {
            $id = reset($id); // Obtiene el primer valor del array
        }
        $values = [$id]; // Inicializamos $values como un array con el id

        //$this->query($sql, $values);

        return $this->query($sql, $values);
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
