<?php
/**
 * Configuración dinámica de la URL base
 * Detecta si el sitio corre en local o en producción para ajustar 
 * automáticamente el protocolo (http/https) y el dominio.
 */

// 1. Detectar si estamos en localhost
$is_localhost = ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

// 2. Definir el protocolo: https para producción (Railway), http para local
$protocol = $is_localhost ? "http" : "https";

// 3. Construir la URL base
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . "/";

// Definición de constantes
define("base_url", $base_url);
define("controller_default", "productoController");
define("action_default", "index");
