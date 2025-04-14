<?php

namespace App\Controllers;

class Controller 
{
    /*public function view($route, $data = null) {
        $route = str_replace('.', '/', $route); // Las rutas se indicarán con puntos en vez de /

        // IMPORTANTE: este código se está ejecuntado desde index.php, por lo que
        // la ruta deberá de especificarse desde allí
        if (file_exists("../resources/views/{$route}.php")) {
            ob_start();
            include "../resources/views/{$route}.php";
            $content = ob_get_clean();

            return $content;
        }
        else {
            echo "La vista no existe";
        }
    }*/

    public function view($route, $data = []) {
        $route = str_replace('.', '/', $route);
    
        if (file_exists("../resources/views/{$route}.php")) {
            ob_start();
            extract($data); // Extrae las variables del array $data para que estén disponibles en la vista
            include "../resources/views/{$route}.php";
            return ob_get_clean();
        } else {
            echo "La vista no existe";
        }
    }

    public function redirect($route) {
        header("Location: {$route}");
    }

    protected function validarDato(string $dato, string $tipo): bool
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
}