<?php

namespace App\Personas;

class Empleado {

    // En esta carpeta se podrían crear más clases para otros tipos de personas
    // Se pueden crear más carpetas en app/ para organizar las clases
    public function __construct(private string $nombre, private string $apellido) {
    }

    }