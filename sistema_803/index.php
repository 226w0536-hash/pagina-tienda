<?php
session_start();
// Habilitar errores para depuración durante el desarrollo en Railway
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

// 1. Determinar el controlador (si no existe, usar el por defecto)
if (isset($_GET['controller'])) {
    $nombre_controlador = $_GET['controller'] . 'Controller';
} else {
    $nombre_controlador = controller_default;
}

// 2. Comprobar si la clase del controlador existe
if (class_exists($nombre_controlador)) {
    $controlador = new $nombre_controlador();
    
    // 3. Determinar la acción (si no existe, usar la por defecto)
    if (isset($_GET['action']) && method_exists($controlador, $_GET['action'])) {
        $action = $_GET['action'];
        $controlador->$action();
    } elseif (!isset($_GET['controller']) && !isset($_GET['action'])) {
        // Carga la acción por defecto si el usuario está en la raíz
        $action_default = action_default;
        $controlador->$action_default();
    } else {
        // La acción no existe o es inválida
        show_error();
    }
} else {
    // El controlador no existe
    show_error();
}

require_once 'views/layout/footer.php';
