<?php

use app\Models\AsientoModel;

if (!isset($asientos) || !empty($asientos)) {
    $AsientoModel = new AsientoModel();
    for ($i = 0; $i <= array_key_last($_POST["asientos"][$id]); $i++) {
        $asientos[$i] = $AsientoModel->all()->where('sala_id', '=', $id)->where('posicion', '=', $_POST["asientos"][$id][$i])->orderBy('id')->get();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>
<link rel="stylesheet" href="__DIR__ . '/../../../css/style.css">

<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <div class="cuerpoformulario-big">
        <h1 class="centered">Editar Asientos</h1>
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
                unset($_SESSION['success']); // Limpiar el mensaje de éxito después de mostrarlo
            }
            ?>
        </div>
        <?php if (isset($asientos) && !empty($asientos)): ?>
            <div class="tabla-resultados">
                <h2>Salas Disponibles</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Sala_id</th>
                            <th>Posicion</th>
                            <th>Precio</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $index = 0; // Añadir esta línea para inicializar $index 
                        ?>
                        <?php foreach ($asientos as $asiento): ?>
                            <tr>
                                <form action="/admin/<?php echo htmlspecialchars($asiento[0]['id']); ?>/edit/update" method="post" enctype="multipart/form-data">
                                    <td><label><?php echo htmlspecialchars($asiento[0]['id']); ?></label></td>
                                    <td><label><?php echo htmlspecialchars($asiento[0]['sala_id']); ?></label></td>
                                    <td><label><?php echo htmlspecialchars($asiento[0]['posicion']); ?></label></td>

                                    <!-- Enviar los datos como parte de un array -->
                                    <input type="hidden" name="asientos[<?php echo $index; ?>][id]" value="<?php echo htmlspecialchars($asiento[0]['id']); ?>">
                                    <input type="hidden" name="asientos[<?php echo $index; ?>][sala_id]" value="<?php echo htmlspecialchars($asiento[0]['sala_id']); ?>">
                                    <input type="hidden" name="asientos[<?php echo $index; ?>][posicion]" value="<?php echo htmlspecialchars($asiento[0]['posicion']); ?>">

                                    <td><input type="text" name="asientos[<?php echo $index; ?>][precio]" value="<?php echo htmlspecialchars($asiento[0]['precio']); ?>"></td>
                            </tr>
                            <?php $index++; // Incrementar el índice después de cada asiento 
                            ?>
                        <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
            <td><input type="submit" value="Enviar"></td>
            </form>
        <?php else: ?>
            <div class="error">
                <p>No hay salas disponibles actualmente.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>

</html>