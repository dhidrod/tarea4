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
                <td></td>
                <td></td>
                <td>Total a pagar: </td>
                <td><?php echo htmlspecialchars(array_sum($salas['precio'])); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
        
        <form action="/cine/<?php echo htmlspecialchars($salas['id']); ?>/comprar/finalizar" method="post">
            <?php foreach ($salas['asiento'] as $asiento): ?>
                <input type="hidden" name="asientos[]" value="<?php echo htmlspecialchars($asiento); ?>">
            <?php endforeach; ?>
            <input type="hidden" name="sala_id" value="<?php echo htmlspecialchars($salas['id']); ?>">
            <input type="hidden" name="fecha_seleccionada" value="<?php echo htmlspecialchars($_POST['fecha_seleccionada']); ?>">
            <input type="hidden" name="precio_total" value="<?php echo htmlspecialchars(array_sum($salas['precio'])); ?>">
            <button type="submit" class="btn-primario">Pagar</button>
        </form>

        <!-- <a href="/cine/<?php //echo htmlspecialchars($salas['id']); ?>/comprar">Completar</a> -->
        <a href="/cine/<?php echo htmlspecialchars($salas['id']); ?>">Volver a la sala</a>


    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>

</html>