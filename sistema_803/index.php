<?php
session_start();
// DEBUG TEMPORAL - Esto te dirá qué está intentando cargar el sistema
echo "Controlador: " . ($_GET['controller'] ?? 'N/A') . "<br>";
echo "Acción: " . ($_GET['action'] ?? 'N/A') . "<br>";
echo "ID: " . ($_GET['id'] ?? 'N/A') . "<br>";
// die(); // Descomenta esto para detener la carga y ver los datos
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
    $error = new ErrorController();
    $error->index();
}

// CORRECCIÓN AQUÍ: Aplicamos ucfirst() para asegurar que la clase empiece con mayúscula
if(isset($_GET['controller'])){
    $nombre_controlador = ucfirst($_GET['controller']) . 'Controller';
}elseif(!isset($_GET['controller']) && !isset($_GET['action'])){
    $nombre_controlador = controller_default;
}else{
    show_error();
    exit();
}

if(class_exists($nombre_controlador)){    
    $controlador = new $nombre_controlador();
    
    // CORRECCIÓN AQUÍ: También validamos el action de forma más limpia
    if(isset($_GET['action']) && method_exists($controlador, $_GET['action'])){
        $action = $_GET['action'];
        $controlador->$action();
    }elseif(!isset($_GET['controller']) && !isset($_GET['action'])){
        $action_default = action_default;
        $controlador->$action_default();
    }else{
        show_error();
    }
}else{
    show_error();
}

require_once 'views/layout/footer.php';


