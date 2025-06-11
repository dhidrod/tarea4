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
        <div class="success">
            <?php
            if (isset($_SESSION['success'])) {
                echo "<p>{$_SESSION['success']}</p>";
                unset($_SESSION['success']); // Limpiar el mensaje de éxito después de mostrarlo
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