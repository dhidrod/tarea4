<!DOCTYPE html>
<html lang="es">
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
        <div class="success">
            <?php
            if (isset($_SESSION['success'])) {
                echo "<p>{$_SESSION['success']}</p>";
                unset($_SESSION['success']);
            }
            ?>
        </div>

        <!-- Navegación entre páginas de salas -->
        <?php if ($totalSalas > 0): ?>
            <div class="navegacion-salas">
                <div class="controles-paginacion">
                    <?php if ($paginaActual > 1): ?>
                        <a href="?p=<?= $paginaActual - 1 ?>&mes=<?= $mes ?>&año=<?= $año ?>&dia=<?= $diaSeleccionado ?>"
                            class="btn-navegacion">&lt; Anterior</a>
                    <?php endif; ?>

                    <div class="paginacion-info">
                        Página <?= $paginaActual ?> de <?= $totalPaginas ?>
                        (Mostrando <?= count($salas) ?> de <?= $totalSalas ?> salas)
                    </div>

                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="?p=<?= $paginaActual + 1 ?>&mes=<?= $mes ?>&año=<?= $año ?>&dia=<?= $diaSeleccionado ?>"
                            class="btn-navegacion">Siguiente &gt;</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Selector de páginas -->
            <?php if ($totalPaginas > 1): ?>
                <div class="selector-paginas">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="?p=<?= $i ?>&mes=<?= $mes ?>&año=<?= $año ?>&dia=<?= $diaSeleccionado ?>"
                            class="btn-pagina <?= ($i == $paginaActual) ? 'activa' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Calendario -->
        <div class="calendario-mejorado">
            <div class="cabecera-calendario">
                <div class="navegacion-mes">
                    <a href="?p=<?= $paginaActual ?>&mes=<?= date('m', strtotime('-1 month', $fechaActual)) ?>&año=<?= date('Y', strtotime('-1 month', $fechaActual)) ?>"
                        class="navegacion-btn">&lt; Mes Anterior</a>
                    <h2><?= strftime('%B %Y', $fechaActual) ?></h2>
                    <a href="?p=<?= $paginaActual ?>&mes=<?= date('m', strtotime('+1 month', $fechaActual)) ?>&año=<?= date('Y', strtotime('+1 month', $fechaActual)) ?>"
                        class="navegacion-btn">Mes Siguiente &gt;</a>
                </div>
                <div class="fecha-seleccionada">
                    Fecha seleccionada: <?= $fechaSeleccionada ?>
                </div>
            </div>

            <div class="dias-semana">
                <?php foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dia): ?>
                    <div><?= $dia ?></div>
                <?php endforeach; ?>
            </div>

            <div class="cuadricula-dias">
                <?php
                $primerDia = date('N', strtotime("$año-$mes-01"));
                for ($i = 1; $i < $primerDia; $i++): ?>
                    <div class="dia-vacio"></div>
                <?php endfor; ?>

                <?php for ($i = 1; $i <= $diasEnMes; $i++):
                    $esHoy = ($i == date('j') && $mes == date('n') && $año == date('Y'));
                ?>
                    <a href="?p=<?= $paginaActual ?>&mes=<?= $mes ?>&año=<?= $año ?>&dia=<?= $i ?>"
                        class="dia-btn <?= ($i == $diaSeleccionado) ? 'seleccionado' : '' ?> <?= $esHoy ? 'hoy' : '' ?>">
                        <?= $i ?>
                        <?php if ($esHoy): ?><div class="indicador-hoy"></div><?php endif; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Asientos -->
        <?php if (!empty($salas)): ?>
            <form method="post" action="/cine/comprar">
                <input type="hidden" name="fecha_seleccionada" value="<?= $año ?>-<?= $mes ?>-<?= $diaSeleccionado ?>">

                <div class="tabla-resultados">
                    <h2>Asientos Disponibles - <?= $fechaSeleccionada ?></h2>
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

                                        // Obtener los asientos ocupados de la sala
                                        $ocupado = false;
                                        foreach ($asientosOcupados as $asientoOcupado) {
                                            if ($sala['id'] == $asientoOcupado['sala_id'] && $asientoOcupado['posicion'] == $contador) {
                                                $ocupado = true;
                                                break;
                                            }
                                        }
                            
                                        echo '<label class="asiento-label">';
                                        echo '<input type="checkbox" 
                                                   name="asientos[' . $sala['id'] . '][]" 
                                                   value="' . $contador . '" 
                                                   ' . ($ocupado ? 'disabled' : '') . '>';
                                        echo '<div class="asiento-btn ' . ($ocupado ? 'ocupado' : '') . '">';
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
                    <button type="submit" class="btn-primario">Continuar con la compra</button>
                </div>
            </form>
        <?php else: ?>
            <div class="mensaje-info">
                No hay salas disponibles para mostrar.
            </div>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>

</body>

</html>