<?php
// Manejo centralizado de errores
/*if (isset($_GET['error']) && $_GET['error'] === 'redirect') {
    header("Location: inicio");
    exit();
}*/


///
require_once __DIR__ . "/../vendor/autoload.php";
//require_once '../vendor/autoload.php';
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
/*$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUser = $_ENV['DB_USER'];
$dbPass = $_ENV['DB_PASS'];
///

try {
    $dsn = "mysql:host=$dbHost;dbname=$dbName";
    $conex = new PDO($dsn, $dbUser, $dbPass);
    $conex->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    //echo 'Salida (dentro del catch): ';
    var_dump($conex->errorInfo());
    echo 'Salida (dentro del catch): ' . $e->getMessage() . PHP_EOL;
}
echo 'Salida (fuera del catch): ';
/*var_dump($conex->errorCode());
var_dump($conex->errorInfo());*/




use Lib\Route;
use App\Controllers\HomeController;
use App\Controllers\UsuarioController;
use App\Controllers\SalaController;
use App\Controllers\AsientoController;
use App\Controllers\EntradaController;

// Crear Base de Datos

Route::get('/database', [HomeController::class, 'makeDatabase']);

// Usuarios

Route::get('/', [HomeController::class, 'index']);
Route::get('/usuario/nuevo', [UsuarioController::class, 'create']);
Route::get('/usuario', [UsuarioController::class, 'index']);
Route::get('/usuario/pruebas', [UsuarioController::class, 'pruebasSQLQueryBuilder']);
//Route::get('/usuario/panel', [UsuarioController::class, 'panel']);


Route::post('/usuario/edit/actualizar/:id', [UsuarioController::class, 'updateUser']);
Route::get('/usuario/edit/:id', [UsuarioController::class, 'edit']);

Route::post('/usuario/saldo/:id/subtractSaldo', [UsuarioController::class, 'subtractSaldo']);
Route::post('/usuario/saldo/:id/addSaldo', [UsuarioController::class, 'addSaldo']);


Route::get('/usuario/saldo/:id', [UsuarioController::class, 'toSaldo']);
//Route::get('/usuario/:id', [UsuarioController::class, 'show']);
Route::get('/usuario/:id', [UsuarioController::class, 'panel']);
Route::post('/usuario', [UsuarioController::class, 'store']);

Route::post('/', [HomeController::class, 'index']);
Route::post('/login', [HomeController::class, 'login']);
Route::post('/logout', [HomeController::class, 'logout']);
Route::post('/usuario/create/add', [UsuarioController::class, 'addUser']);
Route::get('/registro', [HomeController::class, 'toRegistro']);
//Route::get('/registro', [UsuarioController::class, 'create']);

// Cine
//Route::get('/cine', [HomeController::class, 'toCine']);
Route::get('/cine', [SalaController::class, 'toSala']); // Esta ruta manejará /cine?p=1
//Route::get('/cine/:id', [SalaController::class, 'toSala']);

// Asientos
//Route::get('/cine/:id/comprar', [HomeController::class, 'error']); // Redirige a error
Route::get('/cine/comprar', [HomeController::class, 'error']); // Redirige a error
//Route::post('/cine/:id/comprar', [AsientoController::class, 'comprarEntradas']);
Route::post('/cine/comprar', [AsientoController::class, 'comprarEntradas']);
Route::post('/cine/comprar/finalizar', [EntradaController::class, 'terminarCompra']);
//Route::post('/cine/:id/comprar/finalizar', [EntradaController::class, 'terminarCompra']);

// Panel de administración
Route::get('/admin', [HomeController::class, 'toAdmin']);
Route::post('/admin/add', [SalaController::class, 'addSala']);
Route::get('/admin/:id', [HomeController::class, 'toEditSala']);
Route::post('/admin/:id/edit', [HomeController::class, 'toEditAsiento']);
Route::post('/admin/:id/edit/update', [AsientoController::class, 'editarAsiento']);

 
Route::dispatch();















//
/*
$baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request = rtrim($request, '/');

// Normalizar la ruta solicitada
$normalizedRequest = str_replace('..', '', $request); // Eliminar directorios padre
$normalizedRequest = trim($normalizedRequest, '/'); // Eliminar barras iniciales/finales

// Lista blanca de rutas permitidas
$allowed_routes = [
    '' => 'inicio.php',
    'inicio' => 'inicio.php',
    'login' => 'login.php',
    'registro' => 'registro.php',
    'editarpartido' => 'editarpartido.php'
];

// Verificar si la ruta está en la lista blanca
if (isset($allowed_routes[$normalizedRequest])) {
    $file = $baseDir . $allowed_routes[$normalizedRequest];

    // Verificación adicional de seguridad
    $realPath = realpath($file);
    $validPath = realpath($baseDir) . DIRECTORY_SEPARATOR;

    if ($realPath && strpos($realPath, $validPath) === 0 && is_file($realPath)) {
        require $realPath;
    } else {
        //http_response_code(404);
        //require $baseDir . '404.php';
        require $realPath;
    }
} else {
    http_response_code(404);
    require $baseDir . '404.php';
}
*/