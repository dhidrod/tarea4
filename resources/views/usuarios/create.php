<!DOCTYPE html>
<html lang="en">
<?php include_once __DIR__ . '/../head.php'; ?>
<link rel="stylesheet" href="__DIR__ . '/../../css/style.css">

<body>
    <?php include_once __DIR__ . '/../header.php'; ?>

    <div class="cuerpoformulario">
        <h1 class="centered">Registro de Usuarios</h1>
        <h2>
            ¡Regístrate ahora y comienza a administrar tus partidos!
        </h2>
        <div class="formulario">
            <form action="/usuario/create/add" method="post" enctype="multipart/form-data">
                <label for="nombre">Nombre usuario</label>
                <input type="text" name="nombre" id="nombre">
                <label for="apellido1">Primer Apellido</label>
                <input type="text" name="apellido1" id="apellido1">
                <label for="apellido2">Segundo Apellido</label>
                <input type="text" name="apellido2" id="apellido2">
                <label for="nick">Nick (nombre con el que iniciará sesión)</label>
                <input type="text" name="nick" id="nick">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password">
                <label for="password">Repetir contraseña</label>
                <input type="password" name="password2" id="password2">
                <label for="correo">Correo</label>
                <input type="email" name="correo" id="correo">
                <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">
                <input type="submit" value="Enviar">

                <input type="submit" name="generarUsuarios" value="Crear 10 Usuarios Aleatorios">

            </form>
            <div class="error">
                <!-- Mostrar errores si existen -->
                <?php if (isset($_SESSION['error']) && !empty($_SESSION['error'])): ?>
                    <?php echo "<p>" . $_SESSION['error'] . "</p>"; ?>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/../footer.php'; ?>
</body>

</html>