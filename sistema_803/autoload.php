<?php
function controllers_autoload($classname) {
    $directories = ['controllers/', 'models/','config','helpers','models','views'];
    
    foreach ($directories as $dir) {
        $file = $dir . $classname . '.php';
        
        // 1. Intento original (nombre exacto)
        if (file_exists($file)) {
            include_once $file;
            return;
        }
        
        // 2. Si no existe, buscamos el archivo en el directorio 
        // e ignoramos las mayúsculas/minúsculas para encontrar el archivo real
        $files_in_dir = scandir($dir);
        foreach ($files_in_dir as $f) {
            if (strcasecmp($f, $classname . '.php') == 0) {
                include_once $dir . $f;
                return;
            }
        }
    }
}
spl_autoload_register('controllers_autoload');
