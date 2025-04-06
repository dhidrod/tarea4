<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/../head.php'; ?>
<link rel="stylesheet" href="/../../css/style.css">

<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="cuerpoformulario-big">
        <h1 class="centered">Panel de Usuario</h1>

        <?php if (isset($usuario) && !empty($usuario)): ?>
            <div class="tabla-resultados">
                <h2>Información del Usuario</h2>
                
            <!-- Formulario para añadir saldo -->
            <div class="form-saldo">
                <h2>Añadir Saldo</h2>
                <form action="/usuario/saldo/<?php echo $usuario['id']; ?>/addSaldo" method="post">
                    <label for="cantidad">Cantidad a añadir (€):</label>
                    <input type="number" step="0.01" min="0" name="cantidad" id="cantidad" required>
                    <input type="submit" name="addSaldo" value="Añadir Saldo">
                </form>
            </div>

            <!-- Formulario para sustraer saldo -->
            <div class="form-saldo">
                <h2>Restar Saldo</h2>
                <form action="/usuario/saldo/<?php echo $usuario['id']; ?>/subtractSaldo" method="post">
                    <label for="cantidad_restar">Cantidad a restar (€):</label>
                    <input type="number" step="0.01" min="0" name="cantidad" id="cantidad_restar" required>
                    <input type="submit" name="subtractSaldo" value="Restar Saldo">
                </form>
            </div>
        <?php else: ?>
            <div class="error-message">
                <p>No se encontró información del usuario.</p>
            </div>
        <?php endif; ?>
            </div>
    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>

</html>