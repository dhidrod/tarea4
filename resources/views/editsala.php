<?php

use app\Models\SalaModel;
use app\Controllers\AsientoController;
use app\Controllers\EntradaController;

// Creamos una cookie para evitar que se actualicen las entradas cada vez que se carga la página
if (!isset($_COOKIE['entradasActualizadas'])) {
    setcookie('entradasActualizadas', '1', time() + 60);
    $EntradaController = new EntradaController();
    $EntradaController->updateEntradas();
}

// Configuración regional para fechas en español
setlocale(LC_TIME, 'es_ES.utf8');

// Parámetros de fecha
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
$año = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');
$diaSeleccionado = isset($_GET['dia']) ? (int)$_GET['dia'] : date('j');

// Validación de parámetros
$mes = max(1, min(12, $mes));
$año = max(2020, min(2100, $año));
$fechaActual = strtotime("$año-$mes-01");
$diasEnMes = date('t', $fechaActual);
$diaSeleccionado = max(1, min($diasEnMes, $diaSeleccionado));

// Obtener datos de salas
if (!isset($salas) || empty($salas)) {
    $salaModel = new SalaModel();
    $salas = $salaModel->where('id', $id)->get();
    $asientoController = new AsientoController();
    $asientoController->setFechaSeleccionada($año, $mes, $diaSeleccionado);
}

// Fecha formateada
$fechaSeleccionada = strftime('%d/%B/%Y', strtotime("$año-$mes-$diaSeleccionado"));
?>
<!DOCTYPE html>
<html lang="es">
<?php include_once __DIR__ . '/head.php'; ?>
<link rel="stylesheet" href="__DIR__ . '/../../css/style.css">

<body>
    <?php include_once __DIR__ . '/header.php'; ?>

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

        <!-- Asientos -->
        <?php if (isset($salas) && !empty($salas)): ?>
            <form method="post" action="/admin/<?= $id ?>/edit">
                <div class="tabla-resultados">
                    <h2>Asientos</h2>
                    <?php foreach ($salas as $sala): ?>
                        <div class="sala">
                            <h3><?= htmlspecialchars($sala['nombre']) ?> (Capacidad: <?= $sala['capacidad'] ?>)</h3>
                            <div class="indicador-pantalla">Pantalla</div>
                            <div class="tabla-asientos">
                                <?php
                                $asientos_por_fila = 10;
                                $contador = 1;
                                $filas = ceil($sala['capacidad'] / $asientos_por_fila);

                                for ($i = 0; $i < $filas; $i++) {
                                    echo '<div class="fila-asientos">';
                                    for ($j = 0; $j < $asientos_por_fila; $j++) {
                                        if ($contador > $sala['capacidad']) break;

                                        //$ocupado = $asientoController->isAsientoOcupado($sala['id'], $contador);
                                        echo '<label class="asiento-label">';
                                        echo '<input type="checkbox" 
                                                   name="asientos[' . $sala['id'] . '][]" 
                                                   value="' . $contador . '">';
                                        echo '<div class="asiento-btn">';
                                        echo $contador;
                                        echo '</div>';
                                        echo '</label>';
                                        $contador++;
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn-primario">Editar asientos seleccionados</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>

</html>