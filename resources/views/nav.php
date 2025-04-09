<nav>
    <a href="/">Inicio</a>
    <a href="/usuario/nuevo">Registrarse</a>
    <?php if (isset($_SESSION['user'])): ?>
        <a href="/cine">Ver Salas de Cine</a>
    <?php endif; ?>
</nav>