<?php
/*
$error = '';
if (isset($_POST['login'])) {
    $user = isset($_POST['nombre']) ? strtolower($_POST['nombre']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    // Comprobamos si el usuario y la contraseña coinciden con alguno de los usuarios registrados
    /*foreach ($datos as $dato) {
        if ($dato['nombre'] === $user && password_verify($password, $dato['password'])) {
            $_SESSION['user'] = $user;
            break;
        }
    }*//*
    if ($user === "admin" && $password === "1234") {
        $_SESSION['user'] = "admin";
    }
    if (!isset($_SESSION['user'])) {
        $error = 'Usuario o contraseña incorrectos';
    }
}
*/
?>


<!DOCTYPE html>
<html lang="en">
<?php include_once 'head.php'; ?>

<body>
    <?php include_once 'header.php'; ?>

    <div class="cuerpoformulario">




        <?php
        if (isset($_SESSION['user'])) {
            include_once 'main.php';
        } else {
            include_once 'mainLogin.php';
        }

        ?>
        <div class="error">
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p>{$_SESSION['error']}</p>";
                unset($_SESSION['error']); // Limpiar el error después de mostrarlo
            }
            ?>
        </div>

        <div class="success">
            <?php
            if (isset($_SESSION['success'])) {
                echo "<p>{$_SESSION['success']}</p>";
                unset($_SESSION['success']);
            }
            ?>
        </div>


    </div>




    <p class="header-paragraph">Pruebas de consultas (hacer scroll):</p>
    <?php

use App\Models\CineModel;
use app\Models\UsuarioModel; // Recuerda el uso del autoload.php
use app\Models\EntradaModel; // Recuerda el uso del autoload.php

    // Se instancia el modelo
    $usuarioModel = new UsuarioModel();

    // Descomentar consultas para ver la creación. Cuando se lanza execute hay código para
    // mostrar la consulta SQL que se está ejecutando.

    // Consulta 
        $usuarios = $usuarioModel->all()->get();
        // Mostrar los resultados en una tabla
        $prueba1 = $usuarioModel->select('nick')->where('id', '1')->get();
        $prueba2 = $usuarioModel->select('id', 'nick')->get();

        /*echo "ID 0 " . $prueba2[0]["id"] . "<br>";
        echo "NICK 0 " . $prueba2[0]["nick"] . "<br>";
        echo "ID 2 " . $prueba2[2]["id"] . "<br>";
        echo "NICK 2 " . $prueba2[2]["nick"] . "<br>";*/
    ?>

        <h3>Lista de usuarios:</h3>
        <table border='1'>
        <tr>
        <?php
            if (!empty($usuarios)) {
                // Encabezados de la tabla
                foreach (array_keys($usuarios[0]) as $columna) {
                    echo "<th>$columna</th>";
                }
            }
        ?>
        </tr>
        <?php
            if (!empty($usuarios)) {
                // Datos
                foreach ($usuarios as $usuario) {
                    echo "<tr>";
                    foreach ($usuario as $valor) {
                        echo "<td>$valor</td>";
                    }
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No hay usuarios en la base de datos.</td></tr>";
            }
        ?>
        </table>

<?php
/*
 $_SESSION['DEBUG_POST'] = $_POST; // Guardar en sesión para depuración

        // Ajusta los nombres de los campos según tu formulario
        $nickname = $_POST['nickname'] ?? ''; // o podría ser 'nombre', 'username', etc.
        $password = $_POST['password'] ?? ''; // o podría ser 'pass', 'clave', etc.

        $_SESSION['DEBUG_NICKNAME'] = $nickname; // Guardar en sesión para depuración
        $_SESSION['DEBUG_PASSWORD'] = $password; // Guardar en sesión para depuración

        // Instanciamos el modelo
        $usuarioModel = new UsuarioModel();

        // Verificamos las credenciales
        $user = $usuarioModel->checkLogin($nickname, $password);

        // Depuración: verificar qué datos devuelve el modelo
        $_SESSION['DEBUG_USER'] = $user; // Guardar en sesión para depuración

        */

    // Consulta
    //$usuarioModel->select('columna1', 'columna2')->get();

    // Consulta
    //  $usuarioModel->select('columna1', 'columna2')
    //              ->where('columna1', '>', '3')
    //              ->orderBy('columna1', 'DESC')
    //              ->get();

    // Consulta
    //  $usuarioModel->select('id')
    //              ->where('nick', '$_POST[nick]')
    //              ->where('columna2', 'columna3')
    //              ->where('columna2', 'columna3')
    //              ->where('columna3', '!=', 'columna4', 'OR')
    //              ->orderBy('columna1', 'DESC')
    //              ->get();

    // Consulta
    //  $usuarioModel->create(['id' => 1, 'nombre' => 'nombre1', 'apellidos' => 'apellidos1']);
    //$CineModel = new CineModel();
    //$CineModel->create(['saldo' => 10]);
    $EntradaModel = new EntradaModel();
    $data = [
        'usuario_id' => 11,
        'asiento_id' => 5,
        'precio_compra' => 5,
        'fecha_exp' => '2025-04-14'
    ];

    //$EntradaModel->create(['usuario_id' => 11, 'asiento_id' => 5, 'precio_compra' => 10, 'fecha_exp' => '2025-04-14']);
    //$EntradaModel->create($data);
    
    // Consulta
    //$usuarioModel->delete(['id' => 1]);

    // Consulta
    //  $usuarioModel->update(['id' => 1], ['nombre' => 'NombreCambiado']);
    //  $usuarioModel->update(['id' => 11], ['saldo' => '510']);

    echo "Pruebas SQL Query Builder";
    ?>
</body>

</html>