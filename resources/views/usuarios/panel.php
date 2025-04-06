<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/../head.php'; ?>
<link rel="stylesheet" href="__DIR__ . '/../../css/style.css">

<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="cuerpoformulario-big">
        <h1 class="centered">Panel de Usuario</h1>

        <?php if (isset($usuario) && !empty($usuario)): ?>
            <div class="tabla-resultados">
                <h2>Información del Usuario</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nick</th>
                            <th>Nombre</th>
                            <th>Apellido 1</th>
                            <th>Apellido 2</th>
                            <th>Correo</th>
                            <th>Fecha de Nacimiento</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nick']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellido1']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellido2']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($usuario['saldo'], 2)); ?> €</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="acciones-usuario">
                <h2>Acciones</h2>
                <ul>
                    <a href="/usuario/edit/<?php echo $usuario['id']; ?>">Editar perfil</a>
                    <br>
                    <a href="/usuario/saldo">Administrar saldo</a>
                </ul>
            </div>
        <?php else: ?>
            <div class="error-message">
                <p>No se encontró información del usuario.</p>
            </div>
        <?php endif; ?>
    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>

</html>