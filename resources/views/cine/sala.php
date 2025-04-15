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

// Cargar configuración de paginación desde archivo
$configPaginacion = include __DIR__ . '/../../../config/paginacion.php';
$elementosPorPagina = $configPaginacion['salas_por_pagina'] ?? 1; // Valor por defecto si no está configurado

// Parámetros de fecha
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
$año = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');
$diaSeleccionado = isset($_GET['dia']) ? (int)$_GET['dia'] : date('j');

// Parámetro de paginación
$paginaActual = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$paginaActual = max(1, $paginaActual);

// Validación de parámetros de fecha
$mes = max(1, min(12, $mes));
$año = max(2020, min(2100, $año));
$fechaActual = strtotime("$año-$mes-01");
$diasEnMes = date('t', $fechaActual);
$diaSeleccionado = max(1, min($diasEnMes, $diaSeleccionado));

// Obtener datos de todas las salas
$salaModel = new SalaModel();
$todasLasSalas = $salaModel->all()->get();
$totalSalas = count($todasLasSalas);

// Calcular total de páginas
$totalPaginas = ceil($totalSalas / $elementosPorPagina);
$paginaActual = min($paginaActual, max(1, $totalPaginas));

// Obtener las salas para la página actual
$indiceInicio = ($paginaActual - 1) * $elementosPorPagina;
$salas = array_slice($todasLasSalas, $indiceInicio, $elementosPorPagina);

if (!empty($salas)) {
    $asientoController = new AsientoController();
    $asientoController->setFechaSeleccionada($año, $mes, $diaSeleccionado);
}

// Fecha formateada
$fechaSeleccionada = strftime('%d/%B/%Y', strtotime("$año-$mes-$diaSeleccionado"));

// Función para generar URL de paginación
function generarUrlPaginacion($pagina, $mes, $año, $dia) {
    return "?p={$pagina}&mes={$mes}&año={$año}&dia={$dia}";
}
?>
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
                unset($_SESSION['success']); // Limpiar el mensaje de éxito después de mostrarlo
            }
            ?>
        </div>

        <!-- Navegación entre páginas de salas -->
        <?php if ($totalSalas > 0): ?>
            <div class="navegacion-salas">
                <div class="controles-paginacion">
                    <?php if ($paginaActual > 1): ?>
                        <a href="<?= generarUrlPaginacion($paginaActual - 1, $mes, $año, $diaSeleccionado) ?>" 
                           class="btn-navegacion">&lt; Anterior</a>
                    <?php endif; ?>

                    <div class="paginacion-info">
                        Página <?= $paginaActual ?> de <?= $totalPaginas ?>
                        (Mostrando <?= count($salas) ?> de <?= $totalSalas ?> salas)
                    </div>
                    
                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="<?= generarUrlPaginacion($paginaActual + 1, $mes, $año, $diaSeleccionado) ?>" 
                           class="btn-navegacion">Siguiente &gt;</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Selector de páginas -->
            <?php if ($totalPaginas > 1): ?>
                <div class="selector-paginas">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="<?= generarUrlPaginacion($i, $mes, $año, $diaSeleccionado) ?>" 
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

                                        $ocupado = $asientoController->isAsientoOcupado($sala['id'], $contador);
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

    <style>
        .navegacion-salas {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }

        .paginacion-info {
            font-weight: bold;
            font-size: 1.1em;
            margin: 0 15px;
        }

        .controles-paginacion {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        .btn-navegacion {
            padding: 6px 12px;
            background-color: #4a90e2;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
            min-width: 100px;
            text-align: center;
        }

        .btn-navegacion:hover {
            background-color: #357ab8;
        }

        .selector-paginas {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-bottom: 20px;
        }

        .btn-pagina {
            padding: 5px 10px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-decoration: none;
            color: #333;
        }

        .btn-pagina.activa {
            background-color: #4a90e2;
            color: white;
            border-color: #4a90e2;
        }

        .mensaje-info {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin: 20px 0;
            color: #6c757d;
        }
    </style>
</body>

</html>