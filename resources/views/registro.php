<?php

function validarDato(string $dato, string $tipo): bool
{
    switch ($tipo) {
        case 'email':
            $patron = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
            break;

        case 'nombre':
            // Permite letras, acentos, espacios, apóstrofes comunes y números (mín 3 caracteres, máximo 15)
            $patron = '/^[\p{L}\p{N}\s\'-]{3,15}$/u';
            break;

        case 'telefono':
            // Valida números internacionales (opcional + al inicio, de 7 a 15 dígitos)
            $patron = '/^\+?[0-9\s\-]{7,15}$/';
            break;

        case 'password':
            // Mínimo 8 caracteres, al menos una mayúscula, una minúscula, un número y un caracter especial
            $patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
            break;

        case 'fecha':
            // Formato YYYY-MM-DD con validación de fecha real
            return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $dato) && checkdate((int)substr($dato, 5, 2), (int)substr($dato, 8, 2), (int)substr($dato, 0, 4));

        case 'numero':
            // Números enteros o decimales (incluye negativos)
            $patron = '/^-?\d+([.,]\d+)?$/';
            break;
        case 'code':
            // Código formado por cod- seguido de 1 a 5 números
            $patron = '/^cod-\d{1,5}$/';
            break;
        default:
            return false;
    }

    return (bool) preg_match($patron, $dato);
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreUsuario = $_POST["nombreUsuario"];
    $password = $_POST["password"];
    $password2 =  $_POST["password2"];

    // Comprobamos todos los datos y vamos acumulando los errores para mostrarlos luego.
    $errores = [];

    
    
    if (!validarDato($_POST['nombreUsuario'], 'nombre')) {
        $errores[] = 'El nombre de usuario no tiene un formato válido';
    }
    
    if (!validarDato($_POST['password'], 'password')) {
        $errores[] = 'La contraseña no tiene un formato válido';
    }

    
    if ($_POST['password'] !== $_POST['password2']) {
        $errores[] = 'Las contraseñas no coinciden';
    }
    

//// Fin ////

    if (empty($errores)) {
        $filePath = realpath(__DIR__ . "/../../database/users.json");
        
        $productos = json_decode(file_get_contents($filePath), true);
        
        $productos[] = [
            'nombre' => strtolower($nombreUsuario),
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];

        guardarInfo($productos);
    }


////// Generar 10 usuarios aleatorios
    if (isset($_POST['generarUsuarios'])) { 
        $usuariosGenerados = [];
    
        // Bucle para generar 10 usuarios
        for ($i = 0; $i < 10; $i++) {
            $randomName = 'user' . rand(1000, 9999);
            $randomPassword =  password_hash("123456aA?", PASSWORD_DEFAULT);
    
            $usuariosGenerados[] = [
                'nombre' => strtolower($randomName),
                //'password' => password_hash($randomPassword, PASSWORD_DEFAULT)
                'password' => $randomPassword
            ];
            
        }
    
        // Obtiene la ruta absoluta del archivo JSON donde se almacenan los usuarios
        $archivo = realpath(__DIR__ . "/../../database/users.json");
        $usuariosExistentes = []; 
    
        
        if ($archivo !== false && file_exists($archivo)) {
            $json = file_get_contents($archivo);
            $usuariosExistentes = json_decode($json, true) ?: []; 
        }
    
        // Combina los usuarios existentes con los nuevos usuarios generados
        $usuarios = array_merge($usuariosExistentes, $usuariosGenerados);
    
        guardarInfo($usuarios);
    }

}

/*
$error = '';
if (isset($_POST['login'])) {
    $user = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    if ($user === 'admin' && $password === 'admin') {
        $_SESSION['user'] = $user;
    } else {
        $error = 'Usuario o contraseña incorrectos';
    }
}*/
?>
<!DOCTYPE html>
<html lang="en">
<?php include_once 'head.php'; ?>

<body>
    <?php include_once 'header.php'; ?>


    <div class="cuerpoformulario">
        <h1 class="centered">Registro de Usuarios</h1>
        <h2>
            ¡Regístrate ahora y comienza a administrar tus partidos!
        </h2>
        <div class="formulario">
            <form action="" method="post" enctype="multipart/form-data">
                <label for="nombreUsuario">Nombre usuario</label>
                <input type="text" name="nombreUsuario" id="nombreUsuario">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password">
                <label for="password">Repetir contraseña</label>
                <input type="password" name="password2" id="password2">
                <input type="submit" value="Enviar">

                <input type="submit" name="generarUsuarios" value="Crear 10 Usuarios Aleatorios">

            </form>
            <div class= "error">
                <?php
                if (isset($errores)){
                    foreach($errores as $clave => $valor) {
                        echo "<br><p>". $valor ."</p><br>";
                    }
                }
                ?>
            </div>
        </div>
    </div>


    <div class="cuerpo">

        
    </div>
    <?php include_once 'footer.php'; ?>
</body>

</html>
