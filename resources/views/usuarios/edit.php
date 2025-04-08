<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/../head.php'; ?>
<link rel="stylesheet" href="/../../css/style.css">

<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="cuerpoformulario-big">
        
        <div class="error">
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p>{$_SESSION['error']}</p>";
                unset($_SESSION['error']); // Limpiar el error después de mostrarlo
            }
            ?>
        </div>
        <?php if (isset($usuario) && !empty($usuario)): ?>
                

                


        <div class="cuerpoformulario">
            <h1 class="centered">Panel de Usuario</h1>
            <h2>Información del Usuario</h2>
            <div class="formulario">
                <form action="/usuario/edit/actualizar/<?php echo $usuario['id']; ?>" method="post" enctype="multipart/form-data">
                    <label for="nombre">Nombre usuario</label>
                    <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                    <label for="apellido1">Primer Apellido</label>
                    <input type="text" name="apellido1" id="apellido1" value="<?php echo htmlspecialchars($usuario['apellido1']); ?>">
                    <label for="apellido2">Segundo Apellido</label>
                    <input type="text" name="apellido2" id="apellido2" value="<?php echo htmlspecialchars($usuario['apellido2']); ?>">
                    <label for="nick">Nick (nombre con el que iniciará sesión)</label>
                    <input type="text" name="nick" id="nick" value="<?php echo htmlspecialchars($usuario['nick']); ?>">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" id="password">
                    <label for="password">Repetir contraseña</label>
                    <input type="password" name="password2" id="password2">
                    <label for="correo">Correo</label>
                    <input type="email" name="correo" id="correo" value="<?php echo htmlspecialchars($usuario['correo']); ?>">
                    <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario['fecha_nacimiento']); ?>">
                    
                    <input type="submit" value="Enviar">

                </form>
            </div>
        </div>





            <div class="acciones-usuario">
                <h2>Acciones</h2>
                <ul>
                    <a href="/usuario/edit/actualizar/<?php echo $usuario['id']; ?>">Actualizar Datos</a>
                    <br>
                    <a href="/usuario<?php echo $usuario['id']; ?>">Cancelar</a>
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