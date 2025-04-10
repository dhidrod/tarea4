<?php
use app\Models\SalaModel;
use app\Controllers\AsientoController;

if (!isset($salas) || !empty($salas)){
    $salaModel = new SalaModel();
    $salas = $salaModel->where('id', $id)->get();
    //$prueba1 = $usuarioModel->select('nick')->where('id', '1')->get();

    $asientoController = new AsientoController();
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
                unset($_SESSION['error']);
            }
            ?>
        </div>
        
        <?php if (isset($salas) && !empty($salas)): ?>
            <form method="post" action="/cine/<?php echo $id ?>/comprar">
                <div class="tabla-resultados">
                    <h2>Salas Disponibles</h2>
                    <?php foreach ($salas as $sala): ?>
                        <div class="sala">
                            <h3><?php echo $sala['nombre']; ?> (Capacidad: <?php echo $sala['capacidad']; ?>)</h3>
                            <table class="tabla-asientos">
                                <?php
                                $asientos_por_fila = 10;
                                $contador = 1;
                                
                                for ($i = 0; $i < ceil($sala['capacidad'] / $asientos_por_fila); $i++) {
                                    echo '<tr>';
                                    for ($j = 0; $j < $asientos_por_fila; $j++) {
                                        if ($contador > $sala['capacidad']) break;
                                        if ($asientoController->isAsientoOcupado($sala['id'], $contador)) {
                                            echo '<td>
                                                <label class="asiento-label">
                                                    <input type="checkbox" 
                                                           name="asientos[' . $sala['id'] . '][]" 
                                                           value="' . $contador . '" 
                                                           disabled>
                                                    ' . $contador . '
                                                </label>
                                              </td>';
                                        } else {
                                            echo '<td>
                                                <label class="asiento-label">
                                                    <input type="checkbox" 
                                                           name="asientos[' . $sala['id'] . '][]" 
                                                           value="' . $contador . '">
                                                    ' . $contador . '
                                                </label>
                                              </td>';
                                        }
                                        
                                        $contador++;
                                    }
                                    echo '</tr>';
                                }
                                ?>
                            </table>
                        </div><br>
                    <?php endforeach; ?>    
                    <input type="submit" name="comprar" value="Continuar con la compra" style="display: block; float: right;">
                </div>
            </form>
        <?php else: ?>
            <div class="error-message">
                <p>No hay salas disponibles actualmente.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>
</html>