<header>
    <div class="headercontainer">
        <img class="logo" src="/images/logo.png" alt="Logo" style="max-width: 20%; height: auto;" />
        <h1 class="titulo">Cine</h1>


        <?php

        // El token CSRF tiene una duración de 5 minutos. Si el token ha caducado,
        // lo eliminamos. De esta forma, el usuario debe renovar su token al cabo
        // de 10 minutos de inactividad.
        if (isset($_SESSION['token_time']) && $_SESSION['token_time'] < time() - 60 * 5) {
            unset($_SESSION['token']);
            unset($_SESSION['token_time']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Cierre de sesión
            /*if (isset($_POST['logout'])) {
                unset($_SESSION["user"]);
                session_destroy();
                header("Location: /");
                exit();
            }*/

            // Cambio de modo oscuro
            if (isset($_POST['toggle_dark_mode'])) {
                // Alternar el estado del modo oscuro
                $current_dark_mode = isset($_COOKIE['dark_mode']) ? $_COOKIE['dark_mode'] : 'false';
                $new_dark_mode = ($current_dark_mode === 'true') ? 'false' : 'true';

                // Establecer la cookie por 1 minuto
                setcookie('dark_mode', $new_dark_mode, time() + (1 * 60), '/');

                // Recargar la página para reflejar el cambio
                header("Location: /");
                exit();
            }
        }

        // Determinar el estado del modo oscuro
        $dark_mode = isset($_COOKIE['dark_mode']) ? $_COOKIE['dark_mode'] === 'true' : false;
        ?>
        <?php if (isset($_SESSION['user'])): ?>

            <div class="user">
                <p>Bienvenido, <?= $_SESSION['user'] ?></p>

                <form action="/logout" method="post" style="display:inline;">
                    <input type="hidden" name="logout" value="1">
                    <input type="submit" value="Cerrar sesión">
                </form>
                <br>
                    <!-- Botón de modo oscuro -->
                <form method="post">
                    <input type="submit" name="toggle_dark_mode" value="<?= $dark_mode ? 'Modo Claro' : 'Modo Oscuro' ?>">
                </form>
                <br>
                    <!-- Enlace al panel de usuario -->
                <a href="/usuario/<?php echo $_SESSION['user_id'] ?>">Panel de Usuario</a>
            </div>

            

        <?php endif; ?>


    </div>
    <?php include_once 'nav.php'; ?>
    <style>
        body {
            background-color: <?= $dark_mode ? '#1a1a1a' : '#ffffff' ?>;
        }
    </style>
</header>