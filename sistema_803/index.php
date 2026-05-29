<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'autoload.php';
require_once 'config/db.php';
require_once 'config/parameters.php';
require_once 'helpers/utils.php';
require_once 'views/layout/header.php';
require_once 'views/layout/sidebar.php';

function show_error(){
    $error = new errorController();
    $error->index();
}

// 1. Determinar el controlador
if(isset($_GET['controller'])){
    $nombre_controlador = $_GET['controller'] . 'Controller';
} else {
    $nombre_controlador = controller_default;
}

// 2. Ejecución dinámica
if(class_exists($nombre_controlador)){    
    $controlador = new $nombre_controlador();
    
    // 3. Determinar la acción
    if(isset($_GET['action']) && method_exists($controlador, $_GET['action'])){
        $action = $_GET['action'];
        $controlador->$action();
    } elseif(!isset($_GET['controller']) && !isset($_GET['action'])) {
        // Carga por defecto si no hay nada en la URL
        $action_default = action_default;
        $controlador->$action_default();
    } else {
        show_error();
    }
} else {
    show_error();
}

require_once 'views/layout/footer.php';


