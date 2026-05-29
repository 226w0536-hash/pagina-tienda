<?php
/**
 * Define la URL base del proyecto de forma dinámica.
 * Esto detecta automáticamente el protocolo (http/https) y el dominio 
 * (localhost o tu dominio de Railway), eliminando la necesidad de 
 * modificar el código al cambiar de entorno.
 */
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";

define('base_url', 'http://' . $_SERVER['HTTP_HOST'] . '/master-php/proyecto-php-poo/');

/**
 * Define el controlador que se cargará por defecto cuando el usuario 
 * ingrese a la página principal sin especificar ninguna sección.
 */
define("controller_default", "productoController");

/**
 * Define la acción (el método dentro del controlador) que se ejecutará 
 * por defecto, que en este caso suele ser la vista principal o listado.
 */
define("action_default", "index");
