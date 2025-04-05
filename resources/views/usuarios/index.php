<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <a href="/usuario/nuevo">Nuevo usuario</a>
    <h1>Listado usuarios</h1>
    <table>
        <?php
        // $data está definido en Controller.php y pasado en UsuarioController.php
        foreach ($data as $fila) {
            echo "<tr>";
            foreach ($fila as $celda) {
                echo "<td>" . htmlspecialchars($celda) . "</td>";
            }
            echo "</tr>";
        }
        ?>

    </table>
</body>

</html>