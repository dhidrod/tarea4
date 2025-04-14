<?php
use app\Models\SalaModel;

if (!isset($salas) || !empty($salas)){
    $salaModel = new SalaModel();
    $salas = $salaModel->all()->get();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/../head.php'; ?>
<link rel="stylesheet" href="__DIR__ . '/../../css/style.css">

<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="cuerpoformulario-big">
        <h1 class="centered">Salas de Cine</h1>
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
        <?php if (isset($salas) && !empty($salas)): ?>
            <div class="tabla-resultados">
                <h2>Salas Disponibles</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Sala</th>
                            <th>Capacidad</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        <?php foreach ($salas as $sala): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sala['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($sala['capacidad']); ?></td>
                                <td><a href="/cine/<?php echo htmlspecialchars($sala['id']); ?>" class="button">Comprar entrada</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="error">
                <p>No hay salas disponibles actualmente.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>

</html>