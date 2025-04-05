<nav>
    <a href="/">Inicio</a>
    <a href="/usuario/nuevo">Registrarse</a>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="/editarpartido">Ver/Editar partidos</a>
    <?php endif; ?>
</nav>