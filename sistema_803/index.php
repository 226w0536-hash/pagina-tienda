<?php
session_start();

// --- LECTOR DE URL DIRECTO (Inyección manual) ---
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2); 
$path = trim($request_uri[0], '/'); 

if (!empty($path)) {
    $parts = explode('/', $path);
    // Si no existen en $_GET (porque el .htaccess no los pasó), los inyectamos desde la URI
    if (!isset($_GET['controller'])) $_GET['controller'] = $parts[0];
    if (!isset($_GET['action']) && isset($parts[1])) $_GET['action'] = $parts[1];
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'autoload.php';
require_once 'config/db.php';
require_once 'config/parameters.php';
require_once 'helpers/utils.php';
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

/**
 * Función para mostrar la página de error 404
 */
function show_error() {
    $error = new errorController();
    $error->index();
}

/**
 * Lógica principal de enrutamiento
 */

// 1. Determinar el controlador
if (isset($_GET['controller'])) {
    $nombre_controlador = $_GET['controller'] . 'Controller';
} else {
    $nombre_controlador = controller_default;
}

// 2. Comprobar si la clase del controlador existe
if (class_exists($nombre_controlador)) {
    $controlador = new $nombre_controlador();
    
    // 3. Determinar la acción
    if (isset($_GET['action']) && method_exists($controlador, $_GET['action'])) {
        $action = $_GET['action'];
        $controlador->$action();
    } elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {
        $action_default = action_default;
        $controlador->$action_default();
    } else {
        show_error();
    }
} else {
    show_error();
}

require_once 'views/layout/footer.php';
