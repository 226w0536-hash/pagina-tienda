<?php
function app_autoload($classname){
    if (file_exists('controllers/' . $classname . '.php')) {
        include 'controllers/' . $classname . '.php';
    } elseif (file_exists('models/' . $classname . '.php')) {
        include 'models/' . $classname . '.php';
    } elseif (file_exists('helpers/' . $classname . '.php')) {
        include 'helpers/' . $classname . '.php';
    }
}
spl_autoload_register('app_autoload');
