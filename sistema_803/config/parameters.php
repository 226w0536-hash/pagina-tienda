<?php
/**
 * parameters.php
 * * Define la URL base de forma dinámica para evitar errores de 
 * "Mixed Content" (HTTPS vs HTTP) y rutas absolutas incorrectas.
 */

// Detectamos el nombre del dominio o IP
$host = $_SERVER['HTTP_HOST'];

// Detectamos si estamos en la raíz o en una subcarpeta
// Si tu proyecto en Railway está en la raíz, $uri debe ser "/"
// Si estás en local con una subcarpeta como /master-php/proyecto-php-poo/, 
// el código siguiente lo detectará automáticamente.
$scriptName = $_SERVER['SCRIPT_NAME']; // ej: /master-php/proyecto-php-poo/index.php
$uri = str_replace('index.php', '', $scriptName);

// Definimos base_url usando '//' para que el navegador use 
// automáticamente http o https según corresponda.
define('base_url', '//' . $host . $uri);

/**
 * Controladores por defecto
 */
define("controller_default", "productoController");
define("action_default", "index");
