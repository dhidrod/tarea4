<?php
/*use app\Models\SalaModel;

if (!isset($salas) || !empty($salas)){
    $salaModel = new SalaModel();
    $salas = $salaModel->where('id', $id)->get();
    //$prueba1 = $usuarioModel->select('nick')->where('id', '1')->get();
}*/
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/../head.php'; ?>
<link rel="stylesheet" href="/../../css/style.css">

<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="cuerpoformulario-big">
        <h1 class="centered">Resumen de compra</h1>
        <div class="error">
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p>{$_SESSION['error']}</p>";
                unset($_SESSION['error']);
            }
            ?>
        </div>
        

            <div class="tabla-resultados">
                <!--<h2>Resumen de compra</h2>-->
                <table>
                    <thead>
                        <tr>
                            <th>Sala</th>
                            <th>Asiento</th>
                            <th>Día</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <?php for ($i = 0; $i <= array_key_last($salas['asiento']); $i++): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($salas['id']); ?></td>
                                <td><?php echo htmlspecialchars($salas['asiento'][$i]); ?></td>
                                <td><?php echo htmlspecialchars($_POST['fecha_seleccionada']); ?></td>
                                <td><?php echo htmlspecialchars($salas['precio'][$i]); ?></td>
                            </tr>
                        <?php endfor; ?>
                            <td>Total a pagar: </td>
                            <td><?php echo htmlspecialchars(array_sum($salas['precio'])); ?></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>

    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>
</html>