<?php
// Define la URL base del proyecto. Sirve para crear rutas absolutas hacia tus
// archivos, imágenes, estilos (CSS) o scripts (JS), evitando problemas de rutas relativas.
define("base_url", "http://localhost/sistema_803/");
// Define el controlador que se cargará por defecto cuando el usuario 
// ingrese a la página principal sin especificar ninguna sección.
define("controller_default", "productoController");
// Define la acción (el método dentro del controlador) que se ejecutará 
// por defecto, que en este caso suele ser la vista principal o listado.
define("action_default", "index");