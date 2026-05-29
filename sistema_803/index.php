<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- LECTOR DE URL DIRECTO (Versión robusta) ---
$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2); 
$path = trim($request_uri[0], '/'); 

if (!empty($path)) {
    $parts = explode('/', $path);
    
    // 1. Asignar controlador
    if (!isset($_GET['controller'])) $_GET['controller'] = $parts[0];
    
    // 2. Asignar acción y limpiar posibles parámetros pegados (como ver&id=2)
    if (!isset($_GET['action']) && isset($parts[1])) {
        $accion_raw = explode('&', $parts[1]);
        $_GET['action'] = $accion_raw[0];
        
        // Si detectamos parámetros en la ruta, los pasamos a $_GET
        foreach ($accion_raw as $param) {
            if (strpos($param, '=') !== false) {
                list($key, $value) = explode('=', $param);
                $_GET[$key] = $value;
            }
        }
    }
}
// ------------------------------------------------

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
    // Diagnóstico temporal (puedes borrarlo cuando todo funcione)
    echo "<div style='background: red; color: white; padding: 20px;'>";
    echo "ERROR DE ENRUTAMIENTO:<br>";
    echo "Controlador: " . (isset($_GET['controller']) ? $_GET['controller'] : 'Ninguno') . "<br>";
    echo "Acción: " . (isset($_GET['action']) ? $_GET['action'] : 'Ninguna') . "<br>";
    echo "</div>";
    
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

// 2. Comprobar si la clase existe
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
