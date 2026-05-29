<?php
session_start();
// En la primera línea de tu index.php
//var_dump($_GET); 
//die(); // Esto detendrá la página y te mostrará qué parámetros está recibiendo
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

if(isset($_GET['controller'])){
	$nombre_controlador = $_GET['controller'].'Controller';

}elseif(!isset($_GET['controller']) && !isset($_GET['action'])){
	$nombre_controlador = controller_default;
	
}else{
	show_error();
	exit();
}

if(class_exists($nombre_controlador)){	
	$controlador = new $nombre_controlador();
	
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


