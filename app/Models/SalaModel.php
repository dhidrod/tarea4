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



    public function paginate($limit, $offset)
{
   
    $sql = "SELECT * FROM {$this->table} LIMIT $limit OFFSET $offset";
    
    // Ejecutamos la consulta sin parámetros ya que los valores están en la consulta
    $this->query($sql);
    
    // Devolvemos los resultados
    return $this->getResults();
}
}
