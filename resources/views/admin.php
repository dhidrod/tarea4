<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/head.php'; ?>
<link rel="stylesheet" href="__DIR__ . '/../css/style.css">

<body>
    <?php include_once __DIR__ . '/header.php'; ?>

    <div class="cuerpoformulario-big">
        <h1 class="centered">Panel de Administración</h1>
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
                                <td><a href="/admin/<?php echo htmlspecialchars($sala['id']); ?>" class="button">Editar Sala</a></td>
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
        <form action="/admin/add" method="post">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre">
                </div>
                <div class="form-group">
                    <label for="capacidad">Capacidad (entre 1 y 100):</label>
                    <input type="number" id="capacidad" name="capacidad">
                </div>
                <input type="submit" name="addsala" value="Añadir Sala">
        </form>
    </div>

    <?php include_once __DIR__ . '/footer.php'; ?>
</body>

</html>